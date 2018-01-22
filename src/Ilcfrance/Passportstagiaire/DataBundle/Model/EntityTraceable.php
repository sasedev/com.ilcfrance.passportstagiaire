<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Model;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
interface EntityTraceable extends \JsonSerializable
{

    /**
     *
     * @return mixed
     */
    public function getId();

    /**
     *
     * @return array
     */
    public function getRelated();
}