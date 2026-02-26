<?php

namespace App\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use PDO;

class ColumnHydrator extends AbstractHydrator
{
    /**
     * @inheritDoc
     */
    protected function hydrateAllData()
    {
        // TODO: Implement hydrateAllData() method.
        return $this->_stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
