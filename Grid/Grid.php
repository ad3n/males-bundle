<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Grid;

class Grid implements GridInterface
{
    /**
     * @var array
     **/
    protected $records;

    /**
     * @var array
     **/
    protected $header;

    /**
     * @param array $records
     * @return $this
     **/
    public function setRecords(array $records)
    {
        $this->records = $records;

        return $this;
    }

    /**
     * @return array
     **/
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param array $record
     * @return $this
     **/
    public function addRecord(array $record)
    {
        $this->records[] = $record;

        return $this;
    }

    /**
     * @param array $header
     * @return $this
     **/
    public function setHeader(array $header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return array
     **/
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     * @return $this
     **/
    public function addHeader($header)
    {
        $this->header[] = $header;

        return $this;
    }
}