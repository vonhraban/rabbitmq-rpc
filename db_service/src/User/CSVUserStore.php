<?php

namespace Datix\Server\User;

use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;

class CSVUserStore implements UserStore
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * CSVUserStore constructor.
     *
     * @param string $filename File name to load users from
     *
     * @throws CSVReaderException if CSV can not be loaded
     */
    public function __construct($filename)
    {
        try {
            $this->reader = Reader::createFromPath($filename, 'r');
            $this->reader->setHeaderOffset(0);
        }
        catch (Exception $e) {
            throw new CSVReaderException("Error loading CSV file");
        }
    }

    /**
     * Get a user by id
     *
     * @param int $id Id to search against
     *
     * @return array The user
     *
     * @throws CSVReaderException
     */
    public function get($id) {
        if($id < 0) {
            throw new CSVReaderException("Negative IDs are not allowed");
        }

        $record = (new Statement())
            ->where(function($record) use ($id) {
                return $record['id'] == $id;
            })
            ->process($this->reader)
            ->fetchOne();

        if(empty($record)) {
            throw new UserNotFoundException();
        }

        return $record;
    }
}
