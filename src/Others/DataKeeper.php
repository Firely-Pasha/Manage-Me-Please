<?php


namespace App\Others;


class DataKeeper
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getIntField(string $fieldName, bool $nullable = false): ?int
    {
        if (isset($this->data[$fieldName]) && is_numeric($this->data[$fieldName])) {
            return $this->data[$fieldName];
        }

        if ($nullable) {
            return null;
        }
        return 0;
    }

    public function getIntArrayField(string $fieldName, bool $nullable = false): array
    {
        $currentData = $this->data[$fieldName];
        if (isset($currentData) && is_array($currentData)) {
            foreach ($currentData as &$currentItem) {
                if (!is_numeric($currentItem)) {
                    return [];
                }
                $currentItem = (int)$currentItem;
            }
            return $currentData;
        }

        if ($nullable) {
            return null;
        }
        return [];
    }

    public function getStringField(string $fieldName, bool $nullable = false): ?string
    {
        if (isset($this->data[$fieldName]) && is_string($this->data[$fieldName])) {
            return $this->data[$fieldName];
        }

        if ($nullable) {
            return null;
        }

        return '';
    }

    public function getAssocField(string $fieldName, bool $nullable = false): ?array
    {
        if (isset($this->data[$fieldName]) && is_array($this->data[$fieldName])) {
            return $this->data[$fieldName];
        }

        if ($nullable) {
            return null;
        }

        return [];
    }
}