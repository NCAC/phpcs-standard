# Changelog

## [4.1.1](https://github.com/ncac/phpcs-standard/compare/v4.1.0...v4.1.1) (2026-01-06)


### fix

* prevent negative padding in ncac-format display path ([](https://github.com/ncac/phpcs-standard/commit/5826530e77d53337439089e4b21a28c861318a35))

# [4.1.0](https://github.com/ncac/phpcs-standard/compare/v4.0.0...v4.1.0) (2026-01-06)


### chore

* add .gitattributes for optimized package exports ([](https://github.com/ncac/phpcs-standard/commit/a39809c47c706d668f1e17321902d0d3183fec17))


### feat

* add ncac-format command for code formatting ([](https://github.com/ncac/phpcs-standard/commit/66f4c1703fe884e19ed6c298f39a61e75117f740))


### fix

* resolve false positive on HEREDOC/NOWDOC closing markers ([](https://github.com/ncac/phpcs-standard/commit/9f3140ca91d3dd58d1dfd56341f617213ec7fa8b))

# [4.0.0](https://github.com/ncac/phpcs-standard/compare/v3.1.0...v4.0.0) (2025-12-17)


### feat

* add DeclarationSpacingSniff to detect superfluous whitespace ([](https://github.com/ncac/phpcs-standard/commit/3169d7496574816cdad46a4ad1818392f2ce385c)), closes [#24](https://github.com/ncac/phpcs-standard/issues/24)
* implement DeclarationSpacingSniff and fix E2E tests ([](https://github.com/ncac/phpcs-standard/commit/f4f46934aea45aa7276b91e3282c4e7bdd34a2f0)), closes [#24](https://github.com/ncac/phpcs-standard/issues/24)
* Integrate PHP-CS-Fixer and refactor E2E testing strategy ([](https://github.com/ncac/phpcs-standard/commit/08220d47032437cbbab21d54d4d9680f5734560a)), closes [#24](https://github.com/ncac/phpcs-standard/issues/24)


### fix

* exclude detection-only sniffs from E2E .fixed tests ([](https://github.com/ncac/phpcs-standard/commit/7f4a57337e32974d346ef604cf0adde55fe67c1c))
* remove hardcoded /workspace path from E2ETestRunner ([](https://github.com/ncac/phpcs-standard/commit/cdd97541f8dca2592e430bcd72fed6710c4fc6d5))
* resolve NoAlternateControlStructureSniff auto-fix conflicts (#24 #26) ([](https://github.com/ncac/phpcs-standard/commit/c8341dc55d6952c3454c03e16700cd95fd6e206d)), closes [#24](https://github.com/ncac/phpcs-standard/issues/24) [#26](https://github.com/ncac/phpcs-standard/issues/26) [#24](https://github.com/ncac/phpcs-standard/issues/24)
* update SwitchDeclarationSniff .fixed file indentation ([](https://github.com/ncac/phpcs-standard/commit/f97a0dfae2669ba182738466a9237407f908c7a1))

# [3.1.0](https://github.com/ncac/phpcs-standard/compare/v3.0.1...v3.1.0) (2025-12-11)


### chore

* add comprehensive .fixed files and improve test coverage ([](https://github.com/ncac/phpcs-standard/commit/af4e75971eda4e1649ccbe023a78a589588b1e24))
* add detailed logging to DrupalHooksTest for CI debugging ([](https://github.com/ncac/phpcs-standard/commit/665c8ee32abe6683da516ac8e27ea9f07082802f))
* fix coding standards and add phpcsSuppress annotations ([](https://github.com/ncac/phpcs-standard/commit/a4659aad81f9dbbb538649e1c536ab105c627958))
* improve test coverage for Drupal compatibility options ([](https://github.com/ncac/phpcs-standard/commit/e29c98ddbb87cbe62d504df221738657495a1710))


### docs

* add Drupal migration guide ([](https://github.com/ncac/phpcs-standard/commit/1f3811e37279cc84f8bf98a3d86ddb36617fbbd9))


### feat

* add support for Drupal hooks with double underscores ([](https://github.com/ncac/phpcs-standard/commit/58fbf87e7204d1e745204fdce1f00079e8ca2c16))


### fix

* handle PHPCS multi-line output in DrupalHooksTest ([](https://github.com/ncac/phpcs-standard/commit/1a5514f284fb0d184fbb63872d458b6eaa2317a6))

## [3.0.1](https://github.com/ncac/phpcs-standard/compare/v3.0.0...v3.0.1) (2025-12-11)


### chore

* **e2e:** remove debug output from ClassSpacingTest ([](https://github.com/ncac/phpcs-standard/commit/1c1ac4b17a4397b75f2265bddd8df1c3dfedb334))
* minor format ([](https://github.com/ncac/phpcs-standard/commit/f0f3f0a8d71e8c81cd25ee2197453844199c4053))


### fix

* Correct blank line detection in ClassOpeningSpacingSniff with implements ([](https://github.com/ncac/phpcs-standard/commit/07449a8c6de7675bf19f99493b22a4e51f435963))
* **e2e:** correct BadClass test case to have no blank line after opening brace ([](https://github.com/ncac/phpcs-standard/commit/97f0a9467b2e9dcf23e0410f69515d0ee3c2391c))
* **e2e:** make ClassSpacingTest error detection more flexible ([](https://github.com/ncac/phpcs-standard/commit/034a7dfd96aabe5b970111d50e5fcec6ce0114e1))
* **e2e:** remove comment from bad spacing test case ([](https://github.com/ncac/phpcs-standard/commit/8a2096fac86639c59c00e36494cf12255d16aff1))
* **e2e:** search error message in full output instead of line by line ([](https://github.com/ncac/phpcs-standard/commit/08477168e1846cc80642c5beb8334b4e2e0c13b0))
* **e2e:** use explicit line breaks for cross-version consistency ([](https://github.com/ncac/phpcs-standard/commit/9e60f6cf1d90d1ee908acacb8e72ed138cd20630))

# [3.0.0](https://github.com/ncac/phpcs-standard/compare/v1.2.0...v3.0.0) (2025-12-04)


### chore

* add codecov configuration and exclude dev tools ([](https://github.com/ncac/phpcs-standard/commit/ff12537f1b02f4ab83c47ac5ed8cb1604b338bdb))
* clean readme.md ([](https://github.com/ncac/phpcs-standard/commit/60f06126f796c19a1bd9b8506c7866dc30741f34))
* upgrade PHP 8.2 ([](https://github.com/ncac/phpcs-standard/commit/e66ce4322608cca531eb620a419f99e3a03eb490))


### feat

* add comprehensive e2e test suite ([](https://github.com/ncac/phpcs-standard/commit/9da024901823e65e11c7a3e84c904031818c4083))
* require PHP 8.1+ and add comprehensive improvements ([](https://github.com/ncac/phpcs-standard/commit/ebf0098273b857ac6aac7a7d570f425316cd398c)), closes [#18](https://github.com/ncac/phpcs-standard/issues/18)


### fix

* Correct closure indentation detection in TwoSpacesIndentSniff ([](https://github.com/ncac/phpcs-standard/commit/5cca95f9ce2560b19fd81d9fe945f21d5714a2c8))


### refacto

* clean up and optimize NCAC sniffs ([](https://github.com/ncac/phpcs-standard/commit/9c4dfb3df33887fbbc92798321f4be80fb78f1db))


### release

* v1.2.1 ([](https://github.com/ncac/phpcs-standard/commit/057bc7176cf5a046da74122d04e21ff6544067da))


### BREAKING CHANGE

* Minimum PHP version is now 8.1 (was 7.4)

This release consolidates multiple improvements and establishes PHP 8.1+
as the baseline for modern PHP development:

- Require PHP ^8.1 to match PHPUnit 10, Phing 3, and Symfony 6 requirements
- Add comprehensive e2e test suite with Phing integration
- Fix closure indentation detection in TwoSpacesIndentSniff
- Refactor and optimize NCAC sniffs for better performance
- Add codecov configuration and exclude dev tools
- Update CI to test PHP 8.1, 8.2, 8.3 only
- Constrain symfony/console to ^6.0 for PHP 8.1 compatibility
- Add BREAKING_CHANGES.md with migration guide
- Update README.md with PHP 8.1+ requirement

Why PHP 8.1+?
- PHP 7.4 EOL: November 2022
- PHP 8.0 EOL: November 2023
- PHPUnit 10, Phing 3, Symfony 6/7 all require PHP 8.1+
- Aligns with modern PHP ecosystem (Laravel 10+, Symfony 6+)

Migration: Projects on PHP <8.1 should stay on v1.2.0 until they upgrade.

# [2.0.0](https://github.com/ncac/phpcs-standard/compare/v1.2.0...v2.0.0) (2025-12-04)


### chore

* add codecov configuration and exclude dev tools ([](https://github.com/ncac/phpcs-standard/commit/ff12537f1b02f4ab83c47ac5ed8cb1604b338bdb))
* clean readme.md ([](https://github.com/ncac/phpcs-standard/commit/60f06126f796c19a1bd9b8506c7866dc30741f34))
* upgrade PHP 8.2 ([](https://github.com/ncac/phpcs-standard/commit/e66ce4322608cca531eb620a419f99e3a03eb490))


### feat

* add comprehensive e2e test suite ([](https://github.com/ncac/phpcs-standard/commit/9da024901823e65e11c7a3e84c904031818c4083))
* require PHP 8.1+ and add comprehensive improvements ([](https://github.com/ncac/phpcs-standard/commit/b16da8e555e4b31a7d27c96b8b63aba8e0d0f121)), closes [#18](https://github.com/ncac/phpcs-standard/issues/18)


### fix

* Correct closure indentation detection in TwoSpacesIndentSniff ([](https://github.com/ncac/phpcs-standard/commit/5cca95f9ce2560b19fd81d9fe945f21d5714a2c8))


### refacto

* clean up and optimize NCAC sniffs ([](https://github.com/ncac/phpcs-standard/commit/9c4dfb3df33887fbbc92798321f4be80fb78f1db))


### release

* v1.2.1 ([](https://github.com/ncac/phpcs-standard/commit/057bc7176cf5a046da74122d04e21ff6544067da))


### BREAKING CHANGE

* Minimum PHP version is now 8.1 (was 7.4)

This release consolidates multiple improvements and establishes PHP 8.1+
as the baseline for modern PHP development:

- Require PHP ^8.1 to match PHPUnit 10, Phing 3, and Symfony 6 requirements
- Add comprehensive e2e test suite with Phing integration
- Fix closure indentation detection in TwoSpacesIndentSniff
- Refactor and optimize NCAC sniffs for better performance
- Add codecov configuration and exclude dev tools
- Update CI to test PHP 8.1, 8.2, 8.3 only
- Constrain symfony/console to ^6.0 for PHP 8.1 compatibility
- Add BREAKING_CHANGES.md with migration guide
- Update README.md with PHP 8.1+ requirement

Why PHP 8.1+?
- PHP 7.4 EOL: November 2022
- PHP 8.0 EOL: November 2023
- PHPUnit 10, Phing 3, Symfony 6/7 all require PHP 8.1+
- Aligns with modern PHP ecosystem (Laravel 10+, Symfony 6+)

Migration: Projects on PHP <8.1 should stay on v1.2.0 until they upgrade.

# [2.0.0](https://github.com/ncac/phpcs-standard/compare/v1.2.0...v2.0.0) (2025-12-04)

## 🚨 BREAKING CHANGES

* **PHP Version:** Minimum PHP version is now 8.1 (was 7.4)

## Why This Change?

1. **Dependency Requirements**: PHPUnit 10, Phing 3, Symfony 6/7 all require PHP 8.1+
2. **PHP 8.0 EOL**: PHP 8.0 reached End of Life in November 2023
3. **PHP 7.4 EOL**: PHP 7.4 reached End of Life in November 2022
4. **Modern PHP Features**: PHP 8.1+ provides better type safety and features

See [BREAKING_CHANGES.md](BREAKING_CHANGES.md) for migration guide.

## Changes in v2.0.0

### ✨ Features

* add comprehensive e2e test suite ([9da0249](https://github.com/ncac/phpcs-standard/commit/9da024901823e65e11c7a3e84c904031818c4083))

### 🐛 Bug Fixes

* correct closure indentation detection in TwoSpacesIndentSniff ([5cca95f](https://github.com/ncac/phpcs-standard/commit/5cca95f9ce2560b19fd81d9fe945f21d5714a2c8)) - Closes [#18](https://github.com/ncac/phpcs-standard/issues/18)
* require PHP 8.1+ to match dependency requirements

### ♻️ Refactoring

* clean up and optimize NCAC sniffs ([9c4dfb3](https://github.com/ncac/phpcs-standard/commit/9c4dfb3df33887fbbc92798321f4be80fb78f1db))

### 🔧 Maintenance

* add codecov configuration and exclude dev tools ([ff12537](https://github.com/ncac/phpcs-standard/commit/ff12537f1b02f4ab83c47ac5ed8cb1604b338bdb))

---

# [1.2.0](https://github.com/ncac/phpcs-standard/compare/v1.1.1...v1.2.0) (2025-11-05)


### docs

* improve README.md tone and clarity ([](https://github.com/ncac/phpcs-standard/commit/9aa2331c8355bf5ea9d9f71abfca9d903cafaa29))


### feat

* add method chaining indentation ([](https://github.com/ncac/phpcs-standard/commit/361ae8e542e846ca96f4ab275fb0f7a4de78939f))
* add method chaining indentation support to TwoSpacesIndentSniff ([](https://github.com/ncac/phpcs-standard/commit/c61a7f7fe1e1352cd7e76f5e3550f4ece65612b8)), closes [#16](https://github.com/ncac/phpcs-standard/issues/16)


### fix

* There was 1 skipped test ([](https://github.com/ncac/phpcs-standard/commit/dd92ecfd35bedd1e23b3e848f094b92d4061e255))

## [1.1.1](https://github.com/ncac/phpcs-standard/compare/v1.1.0...v1.1.1) (2025-11-04)


### fix

* ClassOpeningSpacingSniff now replaces all whitespace tokens instead of just the last one ([](https://github.com/ncac/phpcs-standard/commit/edf47fff3c5a12f793a75568a51dccd270583da3))
* **ClassOpeningSpacingSniff:** properly handle multiple whitespace tokens in phpcbf ([](https://github.com/ncac/phpcs-standard/commit/28f50fcf79195056c75e9b04cd1703f8162196ce))

# [1.1.0](https://github.com/ncac/phpcs-standard/compare/v1.0.3...v1.1.0) (2025-10-22)


### chore

* add codecov + test + yamlint ([](https://github.com/ncac/phpcs-standard/commit/be008e7fdc99d0516bed31d33ff7402f9cd2eb60))
* ci ([](https://github.com/ncac/phpcs-standard/commit/77ca9f923fae4075450bec4f2a8f78dc5a5e9f76))
* clean .yamllint conf ([](https://github.com/ncac/phpcs-standard/commit/2a915a1f6c7de80c866f565fcc3654d35f328f64))
* enhance doc ([](https://github.com/ncac/phpcs-standard/commit/0b01030625e857d2a91624980353096bd37ae8ec))
* enhance documentation and codecov ([](https://github.com/ncac/phpcs-standard/commit/9716cc6d1562504b8c2e3dae59aec918d370645a))
* enhance format ([](https://github.com/ncac/phpcs-standard/commit/7af00558f72638d4eca13bddf053b087af5e8759))
* fix conf ([](https://github.com/ncac/phpcs-standard/commit/389ced240b1ef4054d21c66b5521b0cba4d2f36b))
* fix generate-env command ([](https://github.com/ncac/phpcs-standard/commit/9a74e0e6b873c12377da0c151cde8203e2cf3064))
* fix xdebug conf ([](https://github.com/ncac/phpcs-standard/commit/17578ba28218a9e8ea86922024e7d1b7317f7ba2))


### fix

* accept return, throw, exit, as valid case/default terminators in SwitchDeclarationSniff ([](https://github.com/ncac/phpcs-standard/commit/23f7a381b53332a0c8a2f9e2b4ce39fc1e722552))
* add missing .yamlint ([](https://github.com/ncac/phpcs-standard/commit/0aa7aa41f78411d792d07030da10cc48b0823e63))
* anonymous classes methodname ([](https://github.com/ncac/phpcs-standard/commit/d6f500b422577f0dba6481fb209addd800fbc79b))
* correct indentation of returned array inside a `case:` ([](https://github.com/ncac/phpcs-standard/commit/220559d8a800ba327ea99a64ad7aafa10b3526c3))
* indent arrays as arguments ([](https://github.com/ncac/phpcs-standard/commit/a820e172abcd3b66bd22dacbced49bc065c3792b))
* Indentation Bug in NCAC.WhiteSpace.TwoSpacesIndent for consecutive `case` instructions ([](https://github.com/ncac/phpcs-standard/commit/9ea5355b6d95c817007ce21b52e952f8eb1dd558))
* Static variables are treated as variables and not method ([](https://github.com/ncac/phpcs-standard/commit/02c6955c59c6a11c0519b6aef1172c18e946ad6b))
* too far in my tests ([](https://github.com/ncac/phpcs-standard/commit/f011998441b42a75ccabb9074653306da5a9a767))

## [1.0.3](https://github.com/ncac/phpcs-standard/compare/v1.0.2...v1.0.3) (2025-10-17)

### feat

- fix standard structure and naming ([](https://github.com/ncac/phpcs-standard/commit/56c8585fd5cde0dcf16c5c96fbee263a0eef0f3e))

## [1.0.2](https://github.com/ncac/phpcs-standard/compare/v1.0.1...v1.0.2) (2025-10-17)

### chore

- update composer.lock after adding extra section ([](https://github.com/ncac/phpcs-standard/commit/f08e223ca867dacc3b2cac28c9fab526358ee9bf))

### feat

- finalize CI badges and infrastructure documentation ([](https://github.com/ncac/phpcs-standard/commit/390441d666998136b32b321e02251208013d38d0))

### fix

- configure NCAC as standard name in composer.json extra section ([](https://github.com/ncac/phpcs-standard/commit/aee39a0fe07b5fee20f0ce6741c2cccf0031354f))

## [1.0.1](https://github.com/ncac/phpcs-standard/compare/v1.0.0...v1.0.1) (2025-10-17)

### fix

- correct package name from phpcs-pretty-standard to phpcs-standard ([](https://github.com/ncac/phpcs-standard/commit/753dc0db6163fb28126486e757c8310af59f22ee))

## 1.0.0 (2025-10-17)

- Initial commit: NCAC PHPCS Standard ([](https://github.com/ncac/phpcs-standard/commit/cca8b50d63583be16915ee50b475762f6b6899c2))

### chore

- configure Husky commit validation and release-it setup ([](https://github.com/ncac/phpcs-standard/commit/09d5d7d99ec28532e0882e04b5bb0c80726e8d66))
- update composer.lock ([](https://github.com/ncac/phpcs-standard/commit/407aa0986140846131494b2d57e21afa2eaea39e))
- update composer.lock after dependency resolution ([](https://github.com/ncac/phpcs-standard/commit/fe9cdabd0374540b57191cb328ac5365900268cb))

### docs

- improve Dev Container setup with environment generation and fix CONTRIBUTING.md ([](https://github.com/ncac/phpcs-standard/commit/6f68aa2c0eb7c6483895474bdb428c73b8e921e4))
- reorganize scripts into scripts/ directory and add comprehensive documentation ([](https://github.com/ncac/phpcs-standard/commit/843f99d1dfa7f5f9b91f3f73622d906ce292495f))
- translate final setup summary to English and synchronize release-it with package.json versions ([](https://github.com/ncac/phpcs-standard/commit/dbbb7959bd0eb91272766a6e0124cf611294f680))

All notable changes to this project will be documented in this file.

This file is automatically generated by release-it and the conventional-changelog plugin.
