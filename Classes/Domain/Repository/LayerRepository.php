<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Domain\Repository;

use Bobosch\OdsOsm\Domain\Model\Layer;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Layer>
 */
class LayerRepository extends Repository
{
   /**
     * @param array $uids
     *
     * @return QueryResultInterface<Layer>
     */

     public function findAllByUids(array $uids): QueryResultInterface
    {
        $q = $this->createQuery();
        $q->matching($q->in('uid', $uids));
        // $q->setOrderings([
        //     'uid' => QueryInterface::ORDER_ASCENDING,
        // ]);
        return $q->execute();
    }
}
