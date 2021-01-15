#!/bin/bash
# build.sh - Run production app build. Will run Composer, NPM, Webpack etc.
#
# CLI variables available $1, $2, $3, $4
# - composer - will build the composer assets before dev start.
# - install - will build the package.json assets before dev start.
# - yarn - will use yarn instead of npm.
# - dashboard - will use the dev dashboard.

# Set false as default for $1
if [ -v $1 ]; then
  set -- "false" "${@:2}" "${@:3:4}"
fi

# Set false as default for $2
if [ -v $2 ]; then
  set -- "${@:1}" "false" "${@:3:4}"
fi

# Set false as default for $3
if [ -v $3 ]; then
  set -- "${@:1:2}" "false" "${@:4}"
fi

# Set false as default for $4
if [ -v $4 ]; then
  set -- "${@:1:2}" "${@:3}" "false"
fi

# Start the build
if [ $1 = "composer" ] || [ $2 = "composer" ] || [ $3 = "composer" ] || [ $4 = "composer" ]; then
  echo "===== BUILD COMPOSER FILES  ==========="
  cd ./plugins/post-type-csv-exporter
  composer install --ignore-platform-reqs

  ## Return to prev dir being repo root dir, ready for next command..
  cd -
fi

cd ./build

if [ $1 = "dashboard" ] || [ $2 = "dashboard" ] || [ $3 = "dashboard" ] || [ $4 = "dashboard" ]; then

  if [ $1 = "install" ] || [ $2 = "install" ] || [ $3 = "install" ] || [ $4 = "install" ]; then
    echo "===== DEV - BUILDING DEPENDENCIES ====="

    if [ $1 = "yarn" ] || [ $2 = "yarn" ] || [ $3 = "yarn" ] || [ $4 = "yarn" ]; then
      echo "===== DEV - USING YARN ====="
      yarn
    else
      echo "===== DEV - USING NPM ====="
      npm install
    fi

  fi

  echo "===== DEV - USING DASHBOARD ==========="

  if [ $1 = "yarn" ] || [ $2 = "yarn" ] || [ $3 = "yarn" ] || [ $4 = "yarn" ]; then
    yarn dev-dashboard
  else
    npm run dev-dashboard
  fi
else

  if [ $1 = "install" ] || [ $2 = "install" ] || [ $3 = "install" ] || [ $4 = "install" ]; then
    echo "===== DEV - BUILDING DEPENDENCIES ====="

    if [ $1 = "yarn" ] || [ $2 = "yarn" ] || [ $3 = "yarn" ] || [ $4 = "yarn" ]; then
      echo "===== DEV - USING YARN ====="
      yarn
    else
      echo "===== DEV - USING NPM ====="
      npm install
    fi

  fi

  echo "===== DEV ====="

  if [ $1 = "yarn" ] || [ $2 = "yarn" ] || [ $3 = "yarn" ] || [ $4 = "yarn" ]; then
    yarn dev
  else
    npm run dev
  fi
fi

## Return to prev dir being repo root dir, ready for next command..
cd -
