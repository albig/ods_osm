<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

use Bobosch\OdsOsm\Domain\Model\Category;
use Bobosch\OdsOsm\Domain\Model\TtAddress;

/**
 * @extends Repository<TtAddress>
 */
class TtAddressRepository extends Repository
{
    /**
     * @return QueryResultInterface<\Bobosch\OdsOsm\Domain\Model\TtAddress>
     */
    public function findByCategory(Category $category): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
            $query->contains('categories', $category->getUid())
        );

        return $query->execute();
    }
}
