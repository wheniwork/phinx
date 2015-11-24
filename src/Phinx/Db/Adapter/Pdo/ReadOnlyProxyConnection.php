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
namespace Phinx\Db\Adapter\Pdo;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read-only PDO Proxy Connection
 *
 * Proxies read operations to an underlying PDO instance and logs write
 * operations without executing them.
 * 
 * @author Matthew Turland <me@matthewturland.com>
 */
class ReadOnlyProxyConnection extends ProxyConnection
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param \PDO $pdo
     * @param OutputInterface $output
     */
    public function __construct(\PDO $pdo, OutputInterface $output)
    {
        parent::__construct($pdo);

        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        return $this->rollBack();
    }

    /**
     * @inheritDoc
     */
    public function exec($statement)
    {
        $verbosity = $this->output->getVerbosity();
        $this->output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
        $this->output->writeln($statement);
        $this->output->setVerbosity($verbosity);
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function prepare($statement, $driver_options = null)
    {
        return new ReadOnlyProxyStatement($statement, $driver_options, $this->output);
    }
}
