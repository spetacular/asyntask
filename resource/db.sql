SET NAMES UTF8;
CREATE TABLE `asyntask` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用.1为启用，0为不启用',
  `type` enum('once','time','loop','long') NOT NULL DEFAULT 'once' COMMENT '任务类型：once即时任务；time定时任务；loop周期任务；long长时任务',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名称',
  `cmd` text NOT NULL COMMENT '任务脚本',
  `params` text NOT NULL COMMENT '参数',
  `lastrun` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次运行时间',
  `nextrun` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下次运行时间',
  `day` tinyint(2) NOT NULL DEFAULT '0' COMMENT '天',
  `hour` tinyint(2) NOT NULL DEFAULT '0' COMMENT '小时',
  `minute` char(36) NOT NULL DEFAULT '' COMMENT '分钟',
  `ret` text NOT NULL COMMENT '执行结果',
  PRIMARY KEY (`id`),
  KEY `nextrun` (`available`,`nextrun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;