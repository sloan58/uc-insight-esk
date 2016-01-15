<?php

namespace App\Exceptions;

use Log;
use Alert;
use Exception;
use Laracasts\Flash\Flash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        /*
         * Soap Fault Exceptions
         */
        if ($e instanceof SoapException) {
            switch($e) {
                case isset($e->message->faultcode) && $e->message->faultcode == 'HTTP':
                    alert()->error('Server Error.  Please check your WSDL version and verify the Username and Password.')->persistent('Close');
                    Log::error('Soap Client Error.', [ 'Incorrect WSDL Version OR authentication error' ]);
                    return redirect()->back();
                    break;

                case isset($e->message->faultstring):
                    alert()->error($e->message->faultstring)->persistent('Close');
                    Log::error('Soap Client Error.', [ $e->message->faultstring ]);
                    return redirect()->back();
                    break;

                default:
                    Flash::error($e->message);
                    alert()->error($e->message)->persistent('Close');
                    Log::error('Soap Client Error.', [ $e->message ]);
                    return redirect()->back();
            }
        }

        /*
         * SQL Query Exceptions
         */
        if($e instanceof SqlQueryException)
        {
            alert()->error('SQL Query Error: ' . $e->message)->persistent('Close');
            Log::error('SQL Query Error: ', [ $e->message ]);
            return redirect()->back();
        }

        /*
         * Twilio Fault Exceptions
         */
        if($e instanceof TwilioException)
        {
            alert()->error('Twilio Service Error: ' . $e->message)->persistent('Close');
            Log::error('Twilio Client Error: ', [ $e->message ]);
            return redirect()->back();
        }

        /*
         * Phone Control Fault Exceptions
         */
        if($e instanceof PhoneDialerException)
        {
            alert()->error('Phone Dialer Error: ' . $e->message)->persistent('Close');
            Log::error('Phone Dialer Error: ', [ $e->message ]);
            return redirect()->back();
        }

        return parent::render($request, $e);
    }
}
