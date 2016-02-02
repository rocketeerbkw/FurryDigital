<?php
use Doctrine\ORM\Mapping as ORM;

/**
 * Sphinx
 *
 * @Table(name="_sphinx")
 * @Entity
 */
class Sphinx extends \FA\Doctrine\Entity
{
    /**
     * @var integer
     *
     * @Column(name="counter_id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $counterId;

    /**
     * @var integer
     *
     * @Column(name="max_doc_id", type="integer", nullable=false)
     */
    protected $maxDocId;

    /**
     * @var string
     *
     * @Column(name="comment", type="string", length=255, nullable=false)
     */
    protected $comment;

}
