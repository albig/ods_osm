<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Domain\Repository;

use Bobosch\OdsOsm\Domain\Model\Layer;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * @extends Repository<Layer>
 */
class LayerRepository extends Repository
{
   /**
     * @param array $uidList
     *
     * @return array<Layer>
     */
     public function findAllByUids(array $uids)
    {
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_odsosm_layer');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_odsosm_layer')
            ->where($queryBuilder->expr()->in('uid', $uids))
            ->getConcreteQueryBuilder()
            ->addOrderBy('FIELD(uid, ' . implode(',', $uids) . ')')
            ->executeQuery()
            ->fetchAllAssociative();

        return $dataMapper->map(Layer::class, $rows);
     }


      /**
     * @param $key
     * @param $uidlist
     * @return array
     */
    protected function orderByKey($key, $uidlist) {
        $order = array();
        foreach ($uidlist as $uid) {
            $order["$key={$uid}"] = QueryInterface::ORDER_DESCENDING;
        }
        return $order;
    }
}
