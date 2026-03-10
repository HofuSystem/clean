<?php

namespace Core\Settings\Exceptions;

use Exception;

class CoreException extends Exception
{
    function __construct($message = "", $errors = null,$data = [],$code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->errors   = $errors;
        $this->data     = $data;
    }

    public $errors  = [];
    public $data    = [];

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // You can log or perform any additional handling here
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->view('errors.custom', ['exception' => $this]);
    }
}