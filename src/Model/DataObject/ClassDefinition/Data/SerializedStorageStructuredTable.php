<?php

declare(strict_types=1);

namespace Factotum\SerializedStorageStructuredTableBundle\Model\DataObject\ClassDefinition\Data;

use Factotum\SerializedStorageStructuredTableBundle\Model\DataObject;
use Pimcore\Model\DataObject as PimcoreDataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\StructuredTable;
use Pimcore\Model\DataObject\Concrete;

class SerializedStorageStructuredTable extends StructuredTable
{
    private const KEY = 'key';
    private const LABEL = 'label';
    private const ROW_IDENTIFIER = '__row_identifyer';
    private const ROW_LABEL = '__row_label';
    private const COLUMN_TYPE_JSON = 'json';
    private const FIELD_TYPE = 'serializedStorageStructuredTable';
    private const PARAM_OWNER = 'owner';
    private const PARAM_FIELDNAME = 'fieldname';
    private const PARAM_LANGUAGE = 'language';
    private const NULLABLE_PREFIX = '?';
    private const NULLABLE_SUFFIX = '|null';
    private const COLUMN_NAME_SEPERATOR = '__';
    private const COLUMN_DATA = 'data';
    private const SERIALIZED_STORAGE_TABLE_CLASS = '\\' . DataObject\Data\SerializedStorageStructuredTable::class;
    private const EMPTY_JSON_ARRAY = '[]';

    /**
     * @param mixed $data
     * @param Concrete|null $object
     * @param array $params
     * @return string[]
     */
    public function getDataForResource(mixed $data, Concrete $object = null, array $params = []): array
    {
        $tableData = [];
        if ($data && is_array($data->getData())) {
            $tableData = $data->getData();
        }

        return [$this->getColumnKeyName() => json_encode($tableData)];
    }

    /**
     * @param mixed $data
     * @param Concrete|null $object
     * @param array $params
     * @return PimcoreDataObject\Data\StructuredTable
     */
    public function getDataFromResource(
        mixed $data,
        Concrete $object = null,
        array $params = []
    ): PimcoreDataObject\Data\StructuredTable {
        $structuredData = $this->decodeJsonData($data);

        return $this->prepareStructuredDataForStorage($params, $structuredData);
    }

    /**
     * @param array $data
     * @return array
     */
    private function decodeJsonData(array $data): array
    {
        return json_decode($data[$this->getColumnKeyName()] ?? self::EMPTY_JSON_ARRAY, true);
    }

    /**
     * @param array $params
     * @param array $structured
     * @return DataObject\Data\SerializedStorageStructuredTable
     */
    private function prepareStructuredDataForStorage(
        array $params,
        array $structured
    ): DataObject\Data\SerializedStorageStructuredTable {
        $table = new DataObject\Data\SerializedStorageStructuredTable($structured);

        if (isset($params[self::PARAM_OWNER])) {
            $table->_setOwner($params[self::PARAM_OWNER])
                ->_setOwnerFieldname($params[self::PARAM_FIELDNAME])
                ->_setOwnerLanguage($params[self::PARAM_LANGUAGE] ?? null);
        }

        return $table;
    }

    /**
     * @return string
     */
    private function getColumnKeyName(): string
    {
        return $this->getName() . self::COLUMN_NAME_SEPERATOR . self::COLUMN_DATA;
    }

    /**
     * @param mixed $data
     * @param Concrete|null $object
     * @param array $params
     * @return array
     */
    public function getDataForEditmode(
        mixed $data,
        Concrete $object = null,
        array $params = []
    ): array {
        if (!($data instanceof PimcoreDataObject\Data\StructuredTable) || $data->isEmpty()) {
            return [];
        }

        return $this->prepareDataForEditMode($data->getData());
    }

    /**
     * @param array $structured
     * @return array
     */
    private function prepareDataForEditMode(array $structured): array
    {
        $editArray = [];

        foreach ($this->getRows() as $row) {
            $rowKey = $row[self::KEY];

            $item = [
                self::ROW_IDENTIFIER => $rowKey,
                self::ROW_LABEL => $row[self::LABEL],
            ];

            foreach ($this->getCols() as $col) {
                $colKey = $col[self::KEY];

                $item[$colKey] = $structured[$rowKey][$colKey] ?? null;
            }

            $editArray[] = $item;
        }

        return $editArray;
    }

    /**
     * @param mixed $data
     * @param Concrete|null $object
     * @param array $params
     * @return PimcoreDataObject\Data\StructuredTable
     */
    public function getDataFromEditmode(
        mixed $data,
        Concrete $object = null,
        array $params = []
    ): PimcoreDataObject\Data\StructuredTable {
        $tableData = [];

        foreach ($data as $line) {
            $rowKey = $line[self::ROW_IDENTIFIER];

            foreach ($this->getCols() as $col) {
                $colKey = $col[self::KEY];
                $tableData[$rowKey][$colKey] = $line[$colKey] ?? null;
            }
        }

        return new DataObject\Data\SerializedStorageStructuredTable($tableData);
    }

    /**
     * @return string|null
     */
    public function getPhpdocReturnType(): ?string
    {
        return self::SERIALIZED_STORAGE_TABLE_CLASS . self::NULLABLE_SUFFIX;
    }

    /**
     * @return string|null
     */
    public function getPhpdocInputType(): ?string
    {
        return self::SERIALIZED_STORAGE_TABLE_CLASS . self::NULLABLE_SUFFIX;
    }

    /**
     * @return string|null
     */
    public function getParameterTypeDeclaration(): ?string
    {
        return self::NULLABLE_PREFIX . self::SERIALIZED_STORAGE_TABLE_CLASS;
    }

    /**
     * @return string|null
     */
    public function getReturnTypeDeclaration(): ?string
    {
        return self::NULLABLE_PREFIX . self::SERIALIZED_STORAGE_TABLE_CLASS;
    }

    /**
     * @return string
     */
    public function getFieldType(): string
    {
        return self::FIELD_TYPE;
    }

    /**
     * @return string[]
     */
    public function getColumnType(): array
    {
        return [self::COLUMN_DATA => self::COLUMN_TYPE_JSON];
    }
}
