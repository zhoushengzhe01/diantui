/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 100137
 Source Host           : localhost:3306
 Source Schema         : diantui

 Target Server Type    : MySQL
 Target Server Version : 100137
 File Encoding         : 65001

 Date: 08/10/2019 14:41:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for webmaster_ad_price_logs
-- ----------------------------
DROP TABLE IF EXISTS `webmaster_ad_price_logs`;
CREATE TABLE `webmaster_ad_price_logs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_auto_price` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `target_price` int(8) NOT NULL DEFAULT 0 COMMENT '目标单价',
  `in_advertiser_price` int(3) NOT NULL DEFAULT 100 COMMENT '收入广告主价格',
  `out_advertiser_price` int(3) NOT NULL DEFAULT 100 COMMENT '支出给站长的广告主价格',
  `hid_height_chance` int(3) NOT NULL DEFAULT 0 COMMENT '暗层计费率',
  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '用户名',
  `ip` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '操作IP',
  `updated_at` timestamp(0) NOT NULL ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  `created_at` timestamp(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
