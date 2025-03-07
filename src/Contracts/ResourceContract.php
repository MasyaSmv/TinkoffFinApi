<?php

namespace TinkoffFinApi\Contracts;

interface ResourceContract
{
    public function all(array $filters = []);
    public function findById($id);
}