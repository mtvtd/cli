# MTVTD Advanced Deploy Script

This script is used to make deployment of the websites easier and more robust. 

------

## Build
- `php mtvtd app:build mtvtd`
- Docs: https://laravel-zero.com/docs/build-a-standalone-application

## Setup this package:
- `composer require mtvtd/mtvtd-cli --dev`
- `php vendor/bin/mtvtd init`
- Create Bitbucket Repo Token
- Add tokens within CircleCI environments
- Update/Create .circleci/config.yml
- Create Trigger within CircleCI with name `composer-schedule`

**CircleCI environment variables:**
```dotenv
GIT_USER=
BITBUCKET_REPO=
BITBUCKET_TOKEN=
```

**Sample .circleci/config.yml**
```yaml
version: 2.1
workflows:
  composer-schedule:
    jobs:
      - composer-update
    when:
      and:
        - equal: [ scheduled_pipeline, << pipeline.trigger_source >> ]
        - equal: [ "composer-schedule", << pipeline.schedule.name >> ]
jobs:
  composer-update:
    docker:
        - image: cimg/php:8.0
    steps:
        - checkout
        - restore_cache:
            keys:
              - v1-dependencies-{{ checksum "composer.lock" }}
              - v1-dependencies-
        - run:
            name: Install Composer Dependecies
            command: composer install --prefer-dist --no-interaction --no-progress --no-scripts --no-suggest
        - save_cache:
            paths:
              - ./vendor
            key: v1-dependencies-{{ checksum "composer.lock" }}
        - run:
            name: Run Composer Update Script
            command: php vendor/bin/mtvtd composer:update
        - run:
            name: Create Bitbucket PR
            command: php vendor/bin/mtvtd bitbucket:create-pr hotfix/composer-update master
```

### Create Bitbucket Repo Token
- Ga naar de "Repository settings"
- Ga naar "Access tokens"
- Klik op "Create Repository Access Token"
- Vink aan: Repository Read & Write & Pull Request Read & Write
- Kopieer de Token & User E-mailadres (voor Push)
- Sla deze op in de ENV vars.

#### Documentation
For full documentation, visit [laravel-zero.com](https://laravel-zero.com/).

#### Links
- https://developer.atlassian.com/cloud/jira/platform/rest/v3/api-group-issue-priorities/#api-rest-api-3-priority-search-get
- https://developer.atlassian.com/cloud/jira/platform/basic-auth-for-rest-apis/
- https://laravel-zero.com/docs/build-a-standalone-application



