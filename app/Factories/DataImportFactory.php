<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Factories;

use Exception;
use Symfony\Component\Yaml\Yaml;
use App\Factories\Contract as BaseFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile as File;

class DataImportFactory extends BaseFactory
{
    /**
     * Directory to temporarily store data files.
     */
    private $rawDataPath;

    /**
     * ...
     */
    protected $rawDataFile;

    /**
     *
     */
    protected $fileName;

    /**
     * Original format of data (e.g. YAML, JSON, etc.)
     */
    protected $rawFormat;

    /**
     * Stores the raw data file to be parsed.
     */
    protected $rawDataString;

    /**
     * Fully parsed data object (including meta data).
     */
    protected $rawDataObject;

    /**
     * Model associated with data.
     */
    protected $dataModel;

    /**
     * Meta data
     */
    protected $dataMeta;

    /**
     * Array containing a set of data.
     */
    protected $dataSet;

    /**
     *
     */
    protected $messages = [];

    /**
     * Called once class has been instantiated.
     */
    public function boot()
    {
        $this->setDataPath(storage_path() .'/app/import');
    }

    /**
     *
     *
     * @param File $rawDataFile
     * @return DataImportFactory
     */
    public function importFromFile(File $rawDataFile)
    {
        $this->setDataFile($rawDataFile);

        // Combined data files.

        // TODO: handle ZIP/TAR files. Create new DataImportFactory object for each data file.
        // and call this method on each object. Maybe: wrap each in their own try/catch block.

        // Move data file so we can manipulate it.
        $movedDataFile = $this->rawDataFile->move($this->rawDataPath, $this->getFileName());

        // Read contents of file.
        $this->setRawData(file_get_contents($this->rawDataPath .'/'. $this->getFileName()));

        // Delete uploaded file.
        unlink($this->rawDataPath .'/'. $this->getFileName());

        // Parse raw data into an array.
        $this->parseRawData();

        // We're now ready to import the data set.
        return $this->importDataSet();
    }

    /**
     *
     */
    public function parseRawData()
    {
        // Performance check.
        if (strlen($this->rawDataString) < 1) {
            throw new Exception('No data received.');
        }

        switch ($this->rawFormat)
        {
            case 'yml':
            case 'yaml':
                $this->rawDataObject = Yaml::parse($this->rawDataString);
                break;

            case 'js':
            case 'json':
                $this->rawDataObject = json_decode($this->rawDataString, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new Exception(json_last_error_msg());
                }
                break;

            case 'bgl':
            case 'dict':
            case 'dictd':
            case 'xml':
            default:
                throw new Exception('Unsuported data format.');
                break;
        }

        // Check that data really was parsed.
        if (!is_array($this->rawDataObject) || empty($this->rawDataObject)) {
            throw new Exception('Invalid data.');
        }

        // Check format of array.
        if (!isset($this->rawDataObject['meta']) || !isset($this->rawDataObject['data'])) {
            throw new Exception('Invalid data format.');
        }

        $this->setDataMeta($this->rawDataObject['meta']);
        $this->setDataArray($this->rawDataObject['data']);
        if (!is_array($this->dataArray) || empty($this->dataArray)) {
            throw new Exception('No data found.');
        }

        // Data integrity check.
        if (!$this->isDataIntegral()) {
            throw new Exception('Checksum failed.');
        }

        // Set data model.
        $this->setDataModel();

        // Remove duplicates.
        $this->dataArray = array_map('unserialize',
                                array_unique(array_map('serialize', $this->dataArray)));
    }

    /**
     * Creates a unique filename for this data set.
     *
     * @return string|null
     */
    public function getFileName()
    {
        if (is_null($this->fileName) && $this->hasValidDataFile())
        {
            // TODO: make file name format a configurable parameter.
            $this->fileName =
                date('Y-m-d') .'-'.
                md5($this->rawDataFile->getBasename()) .'.'.
                $this->rawDataFile->getClientOriginalExtension();
        }

        return $this->fileName;
    }

    /**
     * Sets the path where data files should be uploaded to.
     *
     * @param string $directory
     */
    public function setDataPath($path)
    {
        $this->rawDataPath = $path;
    }

    /**
     * Sets the data file to work with.
     *
     * @param File $rawDataFile
     */
    public function setDataFile(File $rawDataFile)
    {
        $this->rawDataFile = $rawDataFile;

        // Performance check.
        // TODO: check mime type for ZIP/TAR files as well.
        if ($this->rawDataFile->getMimeType() != 'text/plain') {
            throw new Exception('Invalid file type.');
        }

        // We use the file extension only for parsing purposes.
        $this->setDataFormat($this->rawDataFile->getClientOriginalExtension());

        // Reset other properties.
        // TODO
    }

    /**
     *
     *
     * @param string $format
     */
    public function setDataFormat($format)
    {
        $this->rawFormat = strtolower(preg_replace('/[^a-z]/i', '', $format));
    }

    /**
     *
     *
     * @param string $raw
     */
    public function setRawData($raw)
    {
        $this->rawDataString = trim($raw);
    }

    /**
     *
     *
     * @param mixed $model
     */
    public function setDataModel($model = null)
    {
        // Set model from meta data.
        if (!$model)
        {
            switch (strtolower($this->dataMeta['type']))
            {
                case 'language':
                    $this->dataModel = 'App\\Models\\Language';
                    break;

                case 'definition':
                    $this->dataModel = 'App\\Models\\Definition\\Word';
                    break;

                case 'definition/word':
                case 'definition/phrase':
                case 'definition/poem':
                    $this->dataModel = 'App\\Models\\'. str_replace('/', '\\\\', $this->dataMeta['type']);
            }
        }

        // Set model from object.
        // TODO
        elseif (false)
        {

        }

        // Set model directly.
        elseif (is_string($model)) {
            $this->dataModel = $model;
        }
    }

    /**
     * ...
     *
     * @param array $meta
     */
    public function setDataMeta(array $meta)
    {
        $this->dataMeta = $meta;
    }

    /**
     * ...
     *
     * @param array $data
     */
    public function setDataArray(array $data)
    {
        $this->dataArray = $data;
    }

    public function setMessage($msg)
    {
        array_push($this->messages, $msg);
    }

    public function getMessages() {
        return $this->messages;
    }

    /**
     * Returns true if $this->rawDataFile is a valid file.
     *
     * @return bool
     */
    public function hasValidDataFile() {
        return $this->rawDataFile instanceof File;
    }

    public function isDataIntegral()
    {
        // Performance check.
        if (!$this->hasValidDataFile() || !isset($this->dataMeta['checksum'])) {
            return false;
        }

        // Build checksum.
        // TODO: support different checksum algorithms.
        $checksum = md5(json_encode($this->dataArray));

        return $this->dataMeta['checksum'] === $checksum;
    }

    /**
     * Imports a data set into the database.
     *
     * @return DataImportFactory
     */
    public function importDataSet()
    {
        // Performance check.
        if (count($this->dataArray) < 1) {
            throw new Exception('Empty data set.');
        }

        // Since we're in the general DataImportFactory, we will create a new factory that
        // is specific to this data set.
        switch ($this->dataModel)
        {
            case 'App\\Models\\Language':
                $factory = $this->make('LanguageImportFactory');
                break;

            case 'App\\Models\\Definition\\Word':
                $factory = $this->make('Definition\\WordImportFactory');
                break;

            default:
                throw new Exception('Invalid data model.');
        }

        $factory->setDataModel($this->dataModel);
        $factory->setDataMeta($this->dataMeta);
        $factory->setDataArray($this->dataArray);

        // TODO: check for infinite loops?

        return $factory->importDataSet();
    }
}