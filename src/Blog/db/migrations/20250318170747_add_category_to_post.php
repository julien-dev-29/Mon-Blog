<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCategoryToPost extends AbstractMigration
{
    public function change(): void
    {
        $this->table('categories')
            ->addColumn('name', 'string')
            ->addColumn('slug', 'string')
            ->addIndex('slug', ['unique' => true])
            ->create();
    }
}
