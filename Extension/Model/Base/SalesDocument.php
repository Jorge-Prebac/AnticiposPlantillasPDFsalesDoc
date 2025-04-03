<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2013-2022 Carlos Garcia Gomez <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace FacturaScripts\Plugins\AnticiposPlantillasPDFsalesDoc\Extension\Model\Base;

use Closure;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Dinamic\Model\Anticipo as DinAnticipo;

/**
 * Description of SalesDocument
 *
 * @author Jorge-Prebac <info@smartcuines.com>
 */
class SalesDocument
{
	public function Clear(): Closure
    {
		return function() {
			$this->getAdvances();
		};
    }

	public function getAdvances(): Closure
    {
		return function() {
			$modelpc = $this->primaryColumn();
			$codigo = $this->primaryColumnValue();		
			$advance = new DinAnticipo();
			$where = [new DataBaseWhere($modelpc, $codigo)];
			return $advance->all($where, ['id' => 'ASC'], 0, 0);
		};
    }
}