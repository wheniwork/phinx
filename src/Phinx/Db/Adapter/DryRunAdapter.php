<?php
/**
 * Phinx
 *
 * (The MIT license)
 * Copyright (c) 2015 Rob Morgan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated * documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package    Phinx
 * @subpackage Phinx\Db\Adapter
 */
namespace Phinx\Db\Adapter;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\PdoAdapter;
use Phinx\Db\Adapter\Pdo\ReadOnlyProxyConnection;
use Symfony\Component\Console\Output\OutputInterface;

class DryRunAdapter extends AdapterWrapper
{
    /**
     * {@inheritdoc}
     */
    public function getAdapterType()
    {
        return 'DryRunAdapter';
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        if (!$adapter instanceof PdoAdapter) {
            throw new \InvalidArgumentException(
                '--dry-run is currently only supported for PDO connections'
            );
        }

        $adapter->getOutput()->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        parent::setAdapter($adapter);

        $adapter->setConnection($this->getConnection());
    }

    /**
     * @inheritDoc
     */
    public function execute($sql)
    {
        $output = $this->adapter->getOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
        $output->writeln($sql);
        $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function hasTable($tableName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($tableName, $columnName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasIndex($columns, $options = array())
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasForeignKey($tableName, $columns, $constraint = null)
    {
        return true;
    }

    /**
     * @return ReadOnlyProxyConnection
     */
    protected function getConnection()
    {
        return new ReadOnlyProxyConnection(
            $this->adapter->getConnection(),
            $this->adapter->getOutput()
        );
    }
}
