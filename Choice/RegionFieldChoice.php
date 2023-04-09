<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Region\Choice;

use BaksDev\Core\Services\Fields\FieldsChoiceInterface;
use BaksDev\Core\Services\Reference\ReferenceChoiceInterface;
use BaksDev\Reference\Region\Form\RegionFieldForm;
use BaksDev\Reference\Region\Type\Id\RegionUid;

final class RegionFieldChoice implements FieldsChoiceInterface, ReferenceChoiceInterface
{
	
	public function equals($key) : bool
	{
		return $key === RegionUid::TYPE;
	}
	
	public function type() : string
	{
		return RegionUid::TYPE;
	}
	
	public function domain() : string
	{
		return 'reference.region';
	}
	
	
	/** Возвращает класс формы для рендера */
	public function form() : string
	{
		return RegionFieldForm::class;
	}
	
}