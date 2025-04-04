<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Domain\Repository;

use Bobosch\OdsOsm\Domain\Model\Layer;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Layer>
 */
class LayerRepository extends Repository
{
    /**
     * Find all objects by uid in order of given uids.
     *
     * @param array $uids
     * @return array<Layer>
     */
    public function findAllByUids(array $uids)
    {
        if (empty($uids)) {
            return [];
        }

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
}
