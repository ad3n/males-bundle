<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Grid;

interface GridInterface
{
    public function setRecords(array $records);

    public function getRecords();

    public function addRecord(array $record);

    public function setHeader(array $header);

    public function getHeader();

    public function addHeader($header);
}