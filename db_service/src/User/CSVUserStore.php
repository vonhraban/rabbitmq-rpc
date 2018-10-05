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
     * @return User The user
     *
     * @throws \OutOfBoundsException if ID is negative
     * @throws Exception In some very odd and broken situations
     */
    public function get(int $id): User {
        if($id < 0) {
            throw new \OutOfBoundsException("Negative IDs are not allowed");
        }

        $record = (new Statement())
            ->where(function($record) use ($id) {
                // cast to string explicitly
                return $record['id'] === (string) $id;
            })
            ->process($this->reader)
            ->fetchOne();

        if(empty($record)) {
            throw new UserNotFoundException();
        }

        return User::fromArray($record);
    }
}
