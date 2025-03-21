<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCategoryIDToPost extends AbstractMigration
{
    public function change(): void
    {
        $this->table(tableName: 'posts')
            ->addColumn(
                columnName: 'category_id',
                type: 'integer',
                options: [
                    'null' => true,
                    "signed" => false
                ]
            )
            ->addForeignKey(
                columns: 'category_id',
                referencedTable: 'categories',
                referencedColumns: 'id',
                options: [
                    'delete' => 'SET NULL'
                ]
            )
            ->update();
    }
}
