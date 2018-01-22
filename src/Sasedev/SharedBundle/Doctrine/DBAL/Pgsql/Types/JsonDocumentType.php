<?php
namespace Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * JsonDocument
 *
 * @author sasedev <sinus@saseprod.net>
 */
class JsonDocumentType extends JsonType
{

    /**
     *
     * @var string
     */
    const JSONDOCUMENT = 'json_document';

    /**
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     *
     * @var string
     */
    private $format = 'json';

    /**
     *
     * @var array
     */
    private $serializationContext = [];

    /**
     *
     * @var array
     */
    private $deserializationContext = [];

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getName()
    {
        return self::JSONDOCUMENT;
    }

    /**
     * Sets the serializer to use.
     *
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Gets the serializer or throw an \Exception if it isn't available.
     *
     * @throws \RuntimeException
     *
     * @return SerializerInterface
     */
    private function getSerializer()
    {
        if (null === $this->serializer) {
            throw new \RuntimeException(sprintf('An instance of "%s" must be available. Call the "setSerializer" method.', SerializerInterface::class));
        }
        return $this->serializer;
    }

    /**
     * Sets the serialization format (default to "json").
     *
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * Sets the serialization context (default to an empty array).
     *
     * @param array $serializationContext
     */
    public function setSerializationContext(array $serializationContext)
    {
        $this->serializationContext = $serializationContext;
    }

    /**
     * Sets the deserialization context (default to an empty array).
     *
     * @param array $deserializationContext
     */
    public function setDeserializationContext(array $deserializationContext)
    {
        $this->deserializationContext = $deserializationContext;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $this->getSerializer()->serialize($value, $this->format, $this->serializationContext);
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $this->getSerializer()->deserialize($value, '', $this->format, $this->deserializationContext);
    }
}
