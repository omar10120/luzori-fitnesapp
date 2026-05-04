-- 25-12-2025

INSERT INTO `permissions` (`id`, `name`, `title`, `guard_name`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'page-list', 'Page List', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'), (NULL, 'page-add', 'Page Add', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'), (NULL, 'page-edit', 'Page Edit', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'), (NULL, 'page-delete', 'Page Delete', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'), (NULL, 'websitesection', 'Website Section', 'web', NULL, '2025-03-25 12:47:18', '2025-03-25 12:47:18'), (NULL, 'website-section-list', 'Website Section List', 'web', '128', '2025-03-25 12:47:18', '2025-03-25 12:47:18');

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES ('124', '1'), ('125', '1'), ('126', '1'), ('127', '1'), ('129', '1');

-- 26-12-2024

/* INSERT INTO `permissions` (`id`, `name`, `title`, `guard_name`, `parent_id`, `created_at`, `updated_at`) VALUES
(98, 'screen', 'screen', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(99, 'screen-list', 'Screen List', 'web', 98, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(100, 'defaultkeyword', 'Defaultkeyword', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(101, 'defaultkeyword-list', 'Defaultkeyword List', 'web', 100, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(102, 'defaultkeyword-add', 'Defaultkeyword Add', 'web', 100, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(103, 'defaultkeyword-edit', 'Defaultkeyword Edit', 'web', 100, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(104, 'languagelist', 'Languagelist', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(105, 'languagelist-list', 'Languagelist List', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(106, 'languagelist-add', 'Languagelist Add', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(107, 'languagelist-edit', 'Languagelist Edit', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(108, 'languagelist-delete', 'Languagelist Delete', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(109, 'languagewithkeyword', 'Language With Keyword', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(110, 'languagewithkeyword-list', 'Language With Keyword List', 'web', 109, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(111, 'languagewithkeyword-edit', 'Language With Keyword Edit', 'web', 109, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(112, 'bulkimport', 'Bulkimport', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(113, 'bulkimport-add', 'Bulkimport Add', 'web', 112, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(114, 'classschedule', 'Class Schedule', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(115, 'classschedule-list', 'Class Schedule List', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(116, 'classschedule-add', 'Class Schedule Add', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(117, 'classschedule-edit', 'Class Schedule Edit', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(118, 'classschedule-delete', 'Class Schedule Delete', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(119, 'subadmin', 'Sub Admin', 'web', NULL, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(120, 'subadmin-list', 'Sub Admin List', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(121, 'subadmin-add', 'Sub Admin Add', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(122, 'subadmin-edit', 'Sub Admin Edit', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(123, 'subadmin-delete', 'Sub Admin Delete', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08');

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1);
*/ 


-- 08-07-2024
-- notthing to update

-- 24-02-2024
-- INSERT INTO `permissions` (`id`, `name`, `title`, `guard_name`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'subscription-add', 'subscription Add', 'web', '90', '2024-02-26 01:42:52', '2024-02-26 01:42:52');
