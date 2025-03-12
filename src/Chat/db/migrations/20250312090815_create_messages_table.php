<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMessagesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('rooms')
            ->addColumn('name', 'string', ['limit' => 255])
            ->create();

        $table = $this->table('messages');
        $table->addColumn('room_id', 'integer', [
            'signed' => false
        ])
            ->addColumn('sender_id', 'integer')
            ->addColumn('content', 'text')
            ->addColumn('timestamp', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('room_id', 'rooms', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
