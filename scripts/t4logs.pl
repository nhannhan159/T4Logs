#!/usr/bin/perl -w
 
use LWP::UserAgent;
use HTTP::Request;
use HTTP::Response;
use Getopt::Long;
use Log::Fast;
use IO::Select;
use JSON qw( decode_json );
use Data::Dumper;
 
# Command line options
my $options = {
    debug => 0,
    verbose => 0,
    log_file => undef, # will set default later based on settings in DB
    # config_file => "$ROOT_DIR/scripts/config.php",
    save_period => 1,
    save_msg_limit => 0, # no limit
    dbi_debug => 0,
    dump_save => 0,
    help => 0,
    show_eps => 0
};

GetOptions(
    'debug|d=i'            => \$options->{debug},
    'help|h!'              => \$options->{help},
    'verbose|v!'           => \$options->{verbose},
    'log-file|l=s'         => \$options->{log_file},
    'config-file|c=s'      => \$options->{config_file},
    'save-period|sp=i'     => \$options->{save_period},
    'save-msg-limit|sml=i' => \$options->{save_msg_limit},
    'dbi-debug|dd=i'       => \$options->{dbi_debug},
    'dump-save|ds!'        => \$options->{dump_save},
    'show-eps|eps!'        => \$options->{show_eps},
) or usage_and_exit(1); # got some invalid options

if( $options->{help} ) {
    usage_and_exit(0);
}

# Create default logger, will reconfigure it as soon as we read configuration from database
my $log = Log::Fast->global();
setup_log();

# Test some logging
$log->INFO( "Program initialized successfully" );
$log->INFO( "Debug level: $options->{debug}" );

my $input_fh = \*STDIN;
my $select = IO::Select->new();
$select->add($input_fh);

my $eof = 0;
my $recvq = '';
my $saver_pid = 0;
DB::enable_profile() if $ENV{NYTPROF};
while( ! $eof ) {

    my @messages = ();
    my $last_update_time = time();

    # Read messages till we have new second reached
    while( time() - $last_update_time < $options->{save_period} && 
        ( ! $options->{save_msg_limit} || @messages < $options->{save_msg_limit} ) ) {
        # We cannot use getline, as it doesn't cooperate well with io::select, thus
        # we implement it ourself with recvq buffer and regexp getting line by line
        if( $recvq =~ s/(.*)\n// ) {
            my $line = $1;
            push( @messages, process_line( $line ) );
        }
        elsif( $select->can_read( 0.02 ) ) {
            my $n = sysread( $input_fh, $recvq, 4096, length($recvq) );
            if( $n == 0 ) {
                $eof = 1;
                last; # leave inner loop, so we'll call 
                # save_messages before exiting the outer loop
            }
            elsif( $n < 0 ) {
                $log->ERR( "Error reading STDIN: $!" );
                last
            }
        }
    }

    # This will be at most once per $options->{save_period} seconds
    # We call it even if no messages collected - to update rate meter with rate = 0.
    save_messages( @messages );
}
$log->NOTICE( "EOF detected, exiting" );
DB::finish_profile() if $ENV{NYTPROF};

sub usage_and_exit {
    my( $exit_code ) = @_;

    my $myname = $0;
    $myname =~ s{.*/}{}; # leave just program name without path

    # TODO sync this with getopt
    print STDERR qq{
    This program is used to process incoming syslog messages from a file.
    Usage: $myname [-option -option] 
    -h        : this (help) message
    -d        : debug level (0-5) (0 = disabled [default])
    -v        : Also print results to STDERR
    -l        : log file (defaults to dir set in DB settings)
    -c        : config file (defaults to $options->{config_file})
    -sp       : Save Period (how often to dump messages into the DB) (defaults to 1 Second)
    -sml      : Save Message Limit (defaults to no limit)
    -dd       : Print extra debug messages for the Perl DBI
    -ds       : Save all temporary dump files (default is to use a single, random temp name and then remove it after import)
    Example: $myname -l /var/log/foo.log -d 5 -c test/config.php -v
    };
    exit($exit_code);
}

sub DEBUG {
    my( $level, @log_args ) = @_;
    if( $options->{debug} >= $level ) {
        $log->DEBUG( @log_args );
    }
}

sub setup_log {
    my $log_options = {};

    # Setup extra information to put in every log line, depending on debug level
    if( $options->{debug} > 1 ) {
        $log_options->{prefix} = "%D %T %S [%L] ";
    }
    else {
        $log_options->{prefix} = "%D %T [%L] ";
    }

    $log_options->{level} = $options->{debug} > 0 ? 'DEBUG' : 'INFO';
    $log->config( $log_options );
}

sub process_line {

    my $mne = "";
    my $mac = "MAC";
    my( $line ) = @_;
	my $prg = "";
	
    DEBUG( 1, "INCOMING MESSAGE: $line" );

    if ( $line =~ /(\S+ \S+)\t(\S+)\t(\d+)\t(\S+)?.*\t(.*)/ ) {

        # Fields are: TS, Host, PRI, Program, and MSG
        my $ts       = $1;
        my $host     = lc($2);
        my $pri      = $3;
        my $prg      = $4;
        my $msg      = $5;

        # Catch-All:
        $prg = "Syslog" if ((!defined $prg) || ($prg eq "")); 
        $prg =~ s/.*irewall/Firewall/; # Added for Firewalls
        $msg =~ s/\e\[?.*?[\@-~]//g; # Strip ANSI codes
        if( $ts =~ /^(....)-(..)-(..) (..):(..):(..)$/ ) {
            DEBUG(3, "Converting Time from: $ts");
            $ts = POSIX::mktime( $6, $5, $4, $3, $2 - 1, $1 - 1900 );
            DEBUG(3, "Converted Time to: $ts");
        }
        else {
            $log->WARN( "Invalid timestamp in log '$ts', skipping" );
            return;
        }

        $mac = $1 if ( $msg =~ /(([0-9A-F]{2}[:-]){5}([0-9A-F]{2}))/i ); 

        my $facility = int($pri / 8);
        my $severity =    ($pri % 8);

        if ( $msg =~ /^%PIX/ ) {
            $prg = "Cisco ASA";
        }

        if ( $msg =~ /3Com_Firewall/ ) {
            $prg = "3Com Firewall";
            $msg =~ s/\[3Com_Firewall\]?\s(.*)/$1/;
        }

        # OSSEC sends the originating host as part of the message
        if ( $msg =~ /Alert.*?Location: \((.*?)\) ([\d\.]+)/o ) {
            $host = $1;
            $prg  = "OSSEC Security";
        }

        # Handle Snare Format
        my ($facilityname, $eid, $audit_type, $domainController, $category, $msgtext, $userLogin, $userDomain, $source, $type);
        if ( $prg =~ /MSWin_(Security|Application|System)/  ) {
            $log->INFO("Windows Event Detected");
            $facilityname = $1;
            $facility = { Application => 23, Security => 4, System => 3, }->{$facilityname} || 16;
            if (($msg =~ /.*?\d{4}\|(\d+)\|(.*?)\|\\?(.*?)\|.*?\|(.*?)\|(.*?)\|(.*?)\|\|?(.*)/) 
			|| ($msg =~ /.*?(\d{4})\|(.*?)\|\\?(.*?)\|.*?\|(.*?)\|(.*?)\|(.*?)\|\|?(.*)/)) {
                $eid = $1;
                $source = $2;
                $userLogin = $3;
                $type = $4;
                $domainController = $5;
                $category = $6;
                $msgtext = $7;
                $userLogin =~ s/N\/A//g;
                if ($userLogin =~ /(.*?)\\(\w+)/) {
                    $userLogin = "{Host, $1}, {Login Name, $2}";
                }
                $msg = 
                "EventID=" . $eid . ", " . 
                "Source=" . $source . ", " .
                "User=" . $userLogin . ", " .
                "Type=" . $type . ", " .
                "Computer=" . $domainController . ", " .
                "Category=" . $category . ", " .
                "MessageText=" . $msgtext;
            }
        DEBUG( 1, "Windows Event: EventID = '%s', Source = '%s', User = %s, Type = '%s', Computer = '%s', Category = '%s', MessageText = '%s'", $eid, $source, $userLogin, $type, $domainController, $category, $msgtext);
        }
        DEBUG( 1, "mac=%s, ts=%s, host=%s, pri=%s, prg=%s, msg=%s", $mac, $ts, $host, $pri, $prg, $msg );

        $msg =~ s/\\//;     # Some messages come in with a trailing slash 
        $msg =~ s/-Traceback/Traceback/; # Remove - so searches are easier and don't have to escape negative boolean

        $msg =~ s/\t/ /g;   # remove any extra TABs
        $msg =~ s/\177/ /g; # Fix for NT Events Logs (they send 0x7f with the message)

        if ( $prg !~ /Firewall/ && $msg =~ /\%([^:]*\-\d+\-[A-Z\-\_\d]+?)(?:\:|\s)/ ) {
            DEBUG( 1, "Searching for Mnemonic" );
            $mne = $1;
            $prg = "Cisco Syslog";
        } else {
            $mne = "None";
        }
        if ( $msg =~ /#([^:]*\-\d+\-[A-Z\-\_\d]+?)(?:\:|\s)/ ) {
            $mne = $1;
            $prg = "Cisco Wireless";
        }

        # Cisco ASA's send their Mnemonic in the program field...
        if( $prg =~ /%(\w+-\d+-\S+):?/ ) {
            $mne = $1; 
        }

        $prg =~ s/%ACE.*\d+/Cisco ACE/; # Added because ACE modules don't send their program field properly
        $prg =~ s/%ASA.*\d+/Cisco ASA/; # Added because ASA's don't send their program field properly
        $prg =~ s/%FWSM.*\d+/Cisco FWSM/; # Added because FWSM's don't send their program field properly
        $prg =~ s/date=\d+-\d+-\d+/Fortigate Firewall/; # Added because Fortigate's don't follow IETF standards
        $prg =~ s/:$//; # Strip trailing colon from some programs (such as kernel)
        $msg =~ s/time=\d+:\d+:\d+\s//; # Added because Fortigate's don't s follow IETF standards
        $prg =~ s/wc\d+-.*/Cisco Wireless/; # Special for CV - bad wlc's! they send their hostname as a program...ugh!
        $prg =~ s/^\d+$/Cisco Syslog/; # Cisco Messages send the program as an int string.

        # Added below to strip paths from program names so that just the program is listed
        # i.e.: /USR/SBIN/CRON would be inserted into the DB as just CRON
        if ( $prg =~ /\// ) {
            $prg = fileparse($prg);
        }

        # Add filter for Juniper boxes - invalid mnemonics were being picked up.
        if ( $prg =~ /Juniper/ ) {
            $mne = "None";
        }

        # Special fix (urldecode) for any urlencoded strings coming in from VmWare or Apache
        $prg =~ s/%([A-Fa-f0-9]{2})/pack('C', hex($1))/eg;
        if ( !$mne ) {
            $msg =~ s/%([A-Fa-f0-9]{2})/pack('C', hex($1))/eg;
        }

        # Added for Elion to catch ESX
        if ( $host =~ /esx\.vm\.est/ ) {
            $prg = "VMWare";
        }

        # Catch-all for junk streams...
        # This won't work well in non-english environments...
        # $prg = "Unknown" if ($prg !~ /^[\w\'-\s]+$/);
        $prg = 'Unknown' unless ( $prg =~ m{^[-'\w\s]{3,}$} and $prg =~ m{[A-Za-z]{3,}} );
        $prg =~ s/ /_/; # #463 - Spaces in names cause Grids to fail (selector IDs cannot contain a space)
        $eid = 0 if ((!defined $eid) || ($eid eq "")); 

        my $message = {
            host => $host,
            facility => $facility,
            severity => $severity,
            prg => $prg,
            msg => $msg,
            mne => $mne,
            mac => $mac,
            eid => $eid,
            fo => $ts,
            lo => $ts,
            counter => 1,
        };
        DEBUG(1, "Returning Message array as: " . Dumper($message));
        return $message;
    } 
    else {
        $log->WARN( "INVALID MESSAGE FORMAT: '$line'" );
        return;
    }

}

sub save_messages {
    my( @messages ) = @_;
	
    DEBUG( 1, "save_messages, nr of msg: " . scalar(@messages) );

	my $maxid = 0;
	my $ua = new LWP::UserAgent;
	my $request = new HTTP::Request('GET', 'http://127.0.0.1:5984/t4logs/_design/base/_view/maxid');
	my $response = $ua->request($request);
	if ($response->is_success) {
		my $content = decode_json( $response->content );
		$maxid = $content->{'rows'}[0]{'value'};
	} else {
		exit(0);
	}

    for my $msg ( @messages ) {
		$maxid = $maxid + 1;
		$request = new HTTP::Request('POST', 'http://127.0.0.1:5984/t4logs/');
		$json = '{"_id":"' . $maxid . '",'
		. '"host":"' . lc($msg->{host}) .'",'
		. '"facility":"' . $msg->{facility} .'",'
		. '"severity":"' . $msg->{severity} .'",'
		. '"program":"' . $msg->{prg} .'",'
		. '"msg":"' . $msg->{msg} .'",'
		. '"mne":"' . $msg->{mne} .'",'
		. '"eid":"' . $msg->{eid} .'",'
		. '"counter":"' . $msg->{counter} .'",'
		. '"fo":"' . $msg->{fo} .'",'
		. '"lo":"' . $msg->{lo} .'"}';
		$request->header( 'Content-Type' => 'application/json' );
		$request->content( $json );
		$response = $ua->request($request);
    }
}