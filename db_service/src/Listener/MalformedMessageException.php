<?php
namespace Datix\Server\Listener;

class MalformedMessageException extends \RuntimeException
{
    /**
     * @var string Raw data that could not be decoded
     */
    private $rawData;

    /**
     * MalformedMessageException constructor.
     * @param string $rawData
     */
    public function __construct(string $rawData)
    {
        parent::__construct("Received malformed message");
        $this->rawData = $rawData;
    }

    /**
     * Get the data that could not be decoded
     * In real life it would be used for logging purposes
     *
     * @return string
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }
}