<?php


namespace Db\Table;

interface TableInterface
{
	public function getTable();

	public function select($where = null);

	public function insert($set);

	public function update($set, $where = null);

	public function delete($where);
}
