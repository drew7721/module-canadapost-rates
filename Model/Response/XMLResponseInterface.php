<?php
namespace JustinKase\CanadaPostRates\Model\Response;

/**
 * Class XMLResponseInterface
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
interface XMLResponseInterface
{
    const X_PATH_QUOTES = 'price-quotes/price-quote';
    const X_PATH_SERVICE_CODE = 'service-code';
    const X_PATH_SERVICE_NAME = 'service-name';
    const X_PATH_PRICE_DETAILS = 'price-details';
    const X_PATH_DUE = 'due';
    const X_PATH_SERVICE_STANDARD = 'service-standard';
    const X_PATH_EXPECTED_DELIVERY_DATE = 'expected-delivery-date';
}
