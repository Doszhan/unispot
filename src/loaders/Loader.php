<?php

namespace dbsparkleTeam\unispot\loaders;

abstract class Loader
{
    /** @var array */
    protected $fileData;

    public function __construct() {
        $this->init();
    }

    public function init()
    {
        $this->fileData = $this->loadFile();
    }

    public function findById($id)
    {
        if (isset($this->fileData[$id])) {
            return $this->createModel($this->fileData[$id]);
        }

        return null;
    }

    public function findAll()
    {
        $that = $this;

        return array_map(function ($data) use ($that) {
            return $that->createModel($data);
        }, $this->fileData);
    }

    abstract public function loadFile();

    abstract protected function createModel($data);
}
