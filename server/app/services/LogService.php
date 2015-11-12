<?php
namespace PhalconSeed\Services;

use Phalcon\Mvc\User\Component;

use Phalcon\Http\Client\Request;

class LogService extends Component {

    /*
     * Function used to get all logs
     *
     * @return all logs
     */
    public function getAll() {

        $provider  = Request::getProvider();
        $provider->setBaseUri('http://127.0.0.1:5984/t4logs/_design/base/_view/all');
        $provider->header->set('Accept', 'application/json');

        $response = $provider->get('');
        return array(
            "statusCode" => $response->header->statusCode,
            "body" => $response->body
        );
    }

}