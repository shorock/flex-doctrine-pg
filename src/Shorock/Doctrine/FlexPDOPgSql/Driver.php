<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 1/20/17
 * Time: 6:58 PM
 */

namespace Shorock\Doctrine\FlexPDOPgSql;


use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\DBAL\Driver\PDOPgSql\Driver as DoctrineDriver;
use PDO;
use PDOException;

class Driver extends DoctrineDriver
{

    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        if(isset($driverOptions['dsn']) && $driverOptions['dsn'] != '') {
            $dsn = $driverOptions['dsn'];
        } elseif(isset($driverOptions['service']) && $driverOptions['service'] != '') {
            $dsn = 'pgsql:service=' . $driverOptions['service'] . ' ';
        } else {
            /* Fall through if no service or dsn set */
            return parent::connect($params, $username, $password, $driverOptions);
        }

        try {
            $pdo = new PDOConnection(
                $dsn,
                $username,
                $password,
                $driverOptions
            );

            if (defined('PDO::PGSQL_ATTR_DISABLE_PREPARES')
                && (!isset($driverOptions[PDO::PGSQL_ATTR_DISABLE_PREPARES])
                    || true === $driverOptions[PDO::PGSQL_ATTR_DISABLE_PREPARES]
                )
            ) {
                $pdo->setAttribute(PDO::PGSQL_ATTR_DISABLE_PREPARES, true);
            }

            /* defining client_encoding via SET NAMES to avoid inconsistent DSN support
             * - the 'client_encoding' connection param only works with postgres >= 9.1
             * - passing client_encoding via the 'options' param breaks pgbouncer support
             */
            if (isset($params['charset'])) {
                $pdo->query('SET NAMES \'' . $params['charset'] . '\'');
            }

            return $pdo;
        } catch (PDOException $e) {
            throw DBALException::driverException($this, $e);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flex_pdo_pgsql';
    }
}