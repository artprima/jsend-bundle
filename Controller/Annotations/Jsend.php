<?php

/*
 * This file is part of the Artprima Jsend package.
 *
 * (c) Denis Voytyuk <ask@artprima.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Artprima\Bundle\JsendBundle\Controller\Annotations;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class Jsend
 *
 * @author Denis Voytyuk <ask@artprima.cz>
 *
 * @package Artprima\Bundle\JsendBundle\Controller\Annotations
 *
 * Jsend annotation class.
 * @Annotation
 * @Target("METHOD")
 */
class Jsend extends Template
{
    /**
     * When an API call is successful, the JSend object is used as a simple envelope for the results,
     * using the data key
     */
    const STATUS_SUCCESS = 'success';

    /**
     * When an API call is rejected due to invalid data or call conditions,
     * the JSend object's data key contains an object explaining what went wrong,
     * typically a hash of validation errors.
     */
    const STATUS_FAIL = 'fail';

    /**
     * When an API call fails due to an error on the server.
     */
    const STATUS_ERROR = 'error';

    /**
     * @var string
     */
    protected $status = self::STATUS_SUCCESS;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var string
     */
    protected $dataVar = 'records';

    public function getAliasName()
    {
        return 'jsend';
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getDataVar()
    {
        return $this->dataVar;
    }

    /**
     * @param string $dataVar
     */
    public function setDataVar($dataVar)
    {
        $this->dataVar = $dataVar;
    }

}