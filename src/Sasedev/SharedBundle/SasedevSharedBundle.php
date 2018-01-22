<?php
namespace Sasedev\SharedBundle;

use Doctrine\DBAL\Types\Type;
use Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\BooleanArrayType;
use Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\DateIntervalType;
use Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\FloatArrayType;
use Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\HstoreType;
use Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\IntegerArrayType;
use Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\JsonDocumentType;
use Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\TextArrayType;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class SasedevSharedBundle extends Bundle
{

    public function __construct()
    {
        if (Type::hasType(DateIntervalType::DATEINTERVAL)) {
            Type::overrideType(DateIntervalType::DATEINTERVAL, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\DateIntervalType');
        } else {
            Type::addType(DateIntervalType::DATEINTERVAL, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\DateIntervalType');
        }
        if (Type::hasType(HstoreType::HSTORE)) {
            Type::overrideType(HstoreType::HSTORE, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\HstoreType');
        } else {
            Type::addType(HstoreType::HSTORE, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\HstoreType');
        }
        if (Type::hasType(JsonDocumentType::JSONDOCUMENT)) {
            Type::overrideType(JsonDocumentType::JSONDOCUMENT, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\JsonDocumentType');
        } else {
            Type::addType(JsonDocumentType::JSONDOCUMENT, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\JsonDocumentType');
        }
        if (Type::hasType(BooleanArrayType::BOOLEANARRAY)) {
            Type::overrideType(BooleanArrayType::BOOLEANARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\BooleanArrayType');
        } else {
            Type::addType(BooleanArrayType::BOOLEANARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\BooleanArrayType');
        }
        if (Type::hasType(IntegerArrayType::INTEGERARRAY)) {
            Type::overrideType(IntegerArrayType::INTEGERARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\IntegerArrayType');
        } else {
            Type::addType(IntegerArrayType::INTEGERARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\IntegerArrayType');
        }
        if (Type::hasType(FloatArrayType::FLOATARRAY)) {
            Type::overrideType(FloatArrayType::FLOATARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\FloatArrayType');
        } else {
            Type::addType(FloatArrayType::FLOATARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\FloatArrayType');
        }
        if (Type::hasType(TextArrayType::TEXTARRAY)) {
            Type::overrideType(TextArrayType::TEXTARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\TextArrayType');
        } else {
            Type::addType(TextArrayType::TEXTARRAY, 'Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types\TextArrayType');
        }
    }
}
