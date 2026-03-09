/*
 Navicat Premium Data Transfer

 Source Server         : Notebook
 Source Server Type    : MySQL
 Source Server Version : 100017
 Source Host           : 192.168.100.138:3306
 Source Schema         : ita_db

 Target Server Type    : MySQL
 Target Server Version : 100017
 File Encoding         : 65001

 Date: 09/03/2026 11:28:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for logs
-- ----------------------------
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `action` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of logs
-- ----------------------------

-- ----------------------------
-- Table structure for sections
-- ----------------------------
DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sections
-- ----------------------------
INSERT INTO `sections` VALUES (1, 'A', 'การเปิดเผยข้อมูล');
INSERT INTO `sections` VALUES (2, 'B', 'การป้องกันการทุจริต');
INSERT INTO `sections` VALUES (3, 'C', 'การให้บริการประชาชน');
INSERT INTO `sections` VALUES (4, 'D', 'การใช้จ่ายงบประมาณ');
INSERT INTO `sections` VALUES (5, 'E', 'การจัดซื้อจัดจ้าง');
INSERT INTO `sections` VALUES (6, 'F', 'การบริหารงานบุคคล');
INSERT INTO `sections` VALUES (7, 'G', 'ความโปร่งใสในการปฏิบัติงาน');

-- ----------------------------
-- Table structure for upload_logs
-- ----------------------------
DROP TABLE IF EXISTS `upload_logs`;
CREATE TABLE `upload_logs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `action` enum('upload','update','rename','delete') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `section` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT NULL,
  `timestamp` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of upload_logs
-- ----------------------------

-- ----------------------------
-- Table structure for uploaded_documents
-- ----------------------------
DROP TABLE IF EXISTS `uploaded_documents`;
CREATE TABLE `uploaded_documents`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `section_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `uploader_id` int NULL DEFAULT NULL,
  `uploaded_at` datetime NULL DEFAULT NULL,
  `file_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of uploaded_documents
-- ----------------------------

-- ----------------------------
-- Table structure for uploads
-- ----------------------------
DROP TABLE IF EXISTS `uploads`;
CREATE TABLE `uploads`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `section_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `uploaded_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `uploaded_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of uploads
-- ----------------------------
INSERT INTO `uploads` VALUES (6, '685d577b180e7_invoice.pdf', 'D', 'staff', '2025-06-26 21:21:47');
INSERT INTO `uploads` VALUES (7, '685d66ce8def1_credit-report_', 'A', 'staff', '2025-06-26 22:27:10');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','coordinator','staff') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `assigned_sections` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (8, 'admin', '', '$2y$10$ZzzE0Qe.m5qbVNQo0ri5deWAoEw6ggvGxoEeV0VdIWoGgEektc8Ka', 'admin', '', '2026-03-09 11:27:06');
INSERT INTO `users` VALUES (2, 'coordinator', 'นายวิษณุ เสาะสาย', '$2y$10$UKCWDGcU6M6NGeljAASciOeuv5udKvEQwTjo746s.TxRijm.880pW', 'coordinator', '', '2025-06-26 20:00:47');
INSERT INTO `users` VALUES (3, 'staff', 'นายวิษณุ เสาะสาย', '$2y$10$yhEYnsIF5RCeN8ZrtSxZwubAHRBY/oLRjwY/93oF6t5fhkgmTKkA.', 'staff', 'A,B,C,D,E,F,G', '2025-06-26 20:30:45');
INSERT INTO `users` VALUES (6, 'witsanu.soi', 'เด็กชาย วายุ เสาะสาย', '$2y$10$YD2Y0InOQAyukI9W2cPp4eMvjJ8GQWk9MRAhr2rKrZtV9QQmGI0Pa', 'coordinator', NULL, '2025-06-26 23:26:25');

SET FOREIGN_KEY_CHECKS = 1;
