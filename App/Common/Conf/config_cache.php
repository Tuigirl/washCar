<?php

return array(
    //'配置项'=>'配置值'
	/* 系统缓存 */
	//DATA_CACHE_TYPE缓存类型: Apachenote、Apc、Db、Eaccelerator、File、Memcache、Redis、Shmop、Sqlite、Wincache和Xcache
    //'DATA_CACHE_TYPE'                   =>'Memcache',
    'MEMCACHE_HOST'                   	=> '192.168.0.20',
    'MEMCACHE_PORT'                   	=> '11211',

	'DATA_CACHE_TYPE'                   => 'Redis',
	'REDIS_HOST'                        => '127.0.0.1',
	'REDIS_PORT'                        => 6379,

    //缓存有效期
    'DATA_CACHE_TIME'=>3000,
    //缓存前缀
    'DATA_CACHE_PREFIX'=>'',
);
