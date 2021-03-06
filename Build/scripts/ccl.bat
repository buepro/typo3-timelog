echo off
rem *=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
rem *
rem * Creates changelog file for a release
rem * ====================================
rem *
rem * Usage
rem * -----
rem *
rem * 1. Change to project directory (typo3conf/ext/timelog)
rem * 2. Run: Build/scripts/ccl [fromVersion] [targetVersion]
rem *
rem * For escaping sequences see as well https://i.stack.imgur.com/NfH6K.png
rem *
rem *=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
set target=Documentation\Changelog\%2.rst
set gitFeaturesCmd=git log %1..%2 --pretty^=^"* %%s (%%cd, %%h by %%an)^" --date^=format:%%d.%%m.%%Y --abbrev-commit --grep FEATURE
set gitBreaksCmd=git log %1..%2 --pretty^=^"* %%s (%%cd, %%h by %%an)^" --date^=format:%%d.%%m.%%Y --abbrev-commit --grep BREAK
set gitBugfixesCmd=git log %1..%2 --pretty^=^"* %%s (%%cd, %%h by %%an)^" --date^=format:%%d.%%m.%%Y --abbrev-commit --grep BUGFIX
set gitTasksCmd=git log %1..%2 --pretty^=^"* %%s (%%cd, %%h by %%an)^" --date^=format:%%d.%%m.%%Y --abbrev-commit --grep TASK
echo .. include:: ../Includes.txt > %target%
echo. >> %target%
echo .. highlight:: none >> %target%
echo. >> %target%
echo ==================================== >> %target%
echo Changelog for release %2 >> %target%
echo ==================================== >> %target%
echo. >> %target%
echo Features >> %target%
echo ========>> %target%
echo. >> %target%
%gitFeaturesCmd% >> %target%
echo. >> %target%
echo Bugfixes >> %target%
echo ========>> %target%
echo. >> %target%
%gitBugfixesCmd% >> %target%
echo. >> %target%
echo Breaking changes >> %target%
echo ================>> %target%
echo. >> %target%
%gitBreaksCmd% >> %target%
echo. >> %target%
echo Tasks >> %target%
echo =====>> %target%
echo. >> %target%
%gitTasksCmd% >> %target%
echo. >> %target%
echo. >> %target%
echo .. highlight:: shell >> %target%
echo. >> %target%
echo Generated by >> %target%
echo ------------ >> %target%
echo. >> %target%
echo :: >> %target%
echo. >> %target%
echo    %gitFeaturesCmd% >> %target%
echo    %gitBugfixesCmd% >> %target%
echo    %gitBreaksCmd% >> %target%
echo    %gitTasksCmd% >> %target%
echo. >> %target%
echo. >> %target%
echo **Note:** The above list contains just commits marked with FEATURE, BUGFIX, BREAK. Complementary commits are >> %target%
echo available at ^`Github ^<https://github.com/buepro/typo3-timelog/commits/master)^>^`__. >> %target%

