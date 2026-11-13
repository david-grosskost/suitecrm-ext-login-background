# Commands come from suitecrm-devkit, installed by Composer in vendor/:
# make <domain>@<action> runs vendor/david-grosskost/suitecrm-devkit/.make/scripts/<domain>/<action>.sh
# Commands specific to this project go in .make/<domain>.mk (listed with the others).
#
# Bootstrap: on a fresh clone vendor/ is missing, so any make command first runs
# composer install in a container (no PHP needed on the machine), then itself again.

DEVKIT := vendor/david-grosskost/suitecrm-devkit

ifneq ($(wildcard $(DEVKIT)/Makefile),)

include $(DEVKIT)/Makefile

else

.DEFAULT_GOAL := .devkit

# Git Bash: pwd -W gives F:/projects/... (understood by Docker Desktop), and
# MSYS_NO_PATHCONV keeps /app from being turned into a Windows path.
# COMPOSER_ROOT_VERSION: Composer does not need to ask Git for this project's version.
.PHONY: .devkit
.devkit:
	@printf '\n  Devkit absent : composer install (dans Docker)...\n\n'
	@MSYS_NO_PATHCONV=1 docker run --rm --user "$$(id -u):$$(id -g)" \
		-e COMPOSER_ROOT_VERSION=dev-main \
		-v "$$(pwd -W 2>/dev/null || pwd):/app" -w /app \
		composer:2 install --no-interaction --no-progress
	@$(MAKE) --no-print-directory $(MAKECMDGOALS)

# Any other command: install the devkit first, then run it (above).
Makefile: ;
%: .devkit ;

endif
