public function up()
{
    $this->forge->addColumn('aset', [
        'qrcode' => [
            'type'       => 'VARCHAR',
            'constraint' => '255',
            'null'       => true,
            'after'      => 'status',
        ],
    ]);
}

public function down()
{
    $this->forge->dropColumn('aset', 'qrcode');
}