<?php

namespace KRG\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use KRG\CmsBundle\Entity\Validator\FilterWorking;
use KRG\CmsBundle\Entity\Validator\UniqueKey;

/**
 * Filter
 *
 * @ORM\MappedSuperclass(repositoryClass="KRG\CmsBundle\Repository\FilterRepository")
 * @FilterWorking()
 * @UniqueKey()
 * @Gedmo\Loggable
 */
class Filter extends AbstractBlock implements FilterInterface
{
    /**
     * @ORM\Column(type="json_array")
     * @Gedmo\Versioned
     * @var array
     */
    protected $form;

    /**
     * @ORM\OneToOne(targetEntity="KRG\CmsBundle\Entity\SeoInterface", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="seo_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @var SeoInterface
     */
    protected $seo;

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return $this->form['type'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormData()
    {
        return $this->form['data'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPureFormData()
    {
        $pureData = [];
        foreach ($this->getFormData() as $name => $data) {
            if (preg_match('/\[([^\]]*)\]/', $name, $match)) {
                $pureData[$match[1]] = $data;
            }
        }

        return $pureData;
    }

    /**
     * {@inheritdoc}
     */
    public function setForm(array $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function setSeo(SeoInterface $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeo()
    {
        return $this->seo;
    }
}
