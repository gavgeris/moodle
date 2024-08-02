#! /bin/sh

# mods
git submodule add https://github.com/gavgeris/moodle-mod_certificate mod/certificate
git submodule add https://github.com/learnweb/moodle-mod_groupmembers mod/groupmembers
git submodule add https://github.com/PoetOS/moodle-mod_questionnaire mod/questionnaire
git submodule add https://github.com/moodleworkplace/moodle-mod_coursecertificate mod/coursecertificate

#blocks
git submodule add https://github.com/jonof/moodle-block_completion_progress blocks/completion_progress
git submodule add https://github.com/UniversityofPortland/moodle-block_custom_course_menu blocks/custom_course_menu
git submodule add https://github.com/remotelearner/moodle-block_grade_me blocks/grade_me
git submodule add https://github.com/Syxton/moodle-block_massaction blocks/massaction
git submodule add https://github.com/donhinkelman/moodle-block_sharing_cart blocks/sharing_cart
git submodule add https://github.com/FMCorz/moodle-block_xp blocks/xp

#filters
git submodule add https://github.com/michael-milette/moodle-filter_filtercodes filter/filtercodes
git submodule add https://github.com/justinhunt/moodle-filter_generico filter/generico
git submodule add https://github.com/gavgeris/reusecontent filter/reusecontent
git submodule add https://github.com/Syxton/moodle-filter_sectionnames filter/sectionnames
git submodule add https://github.com/sharpchi/moodle-filter_syntaxhighlighter filter/syntaxhighlighter
git submodule add https://github.com/iarenaza/moodle-filter_multilang2 filter/multilang2

#atto plugins
git submodule add https://github.com/johnhpercival/moodle-atto_bsgrid lib/editor/atto/plugins/bsgrid
git submodule add https://github.com/dthies/moodle-atto_fullscreen lib/editor/atto/plugins/fullscreen
git submodule add https://github.com/justinhunt/moodle-atto_generico lib/editor/atto/plugins/generico
git submodule add https://github.com/ndunand/moodle-atto_morebackcolors lib/editor/atto/plugins/morebackcolors
git submodule add https://github.com/moodle-an-hochschulen/moodle-atto_styles lib/editor/atto/plugins/styles
git submodule add https://github.com/sharpchi/moodle-atto_templates4u lib/editor/atto/plugins/templates4u
git submodule add https://github.com/rogersegu/moodle-atto_c4l/ lib/editor/atto/plugins/c4l


#tools
git submodule add https://github.com/doiphode/moodle-tool_clearbackupfiles admin/tool/clearbackupfiles
git submodule add https://github.com/moodle-an-hochschulen/moodle-tool_redis admin/tool/redis
git submodule add https://github.com/moodleworkplace/moodle-tool_certificate admin/tool/certificate

#course formats
git submodule add https://github.com/cellule-tice/moodle-format_collapsibletopics course/format/collapsibletopics
git submodule add https://github.com/gavgeris/moodle-format_remuiformat.git course/format/remuiformat
git submodule add https://github.com/gjb2048/moodle-format_topcoll.git course/format/topcoll

#themes
git submodule add https://gitlab.com/jezhops/moodle-theme_adaptable.git theme/adaptable
git submodule add https://github.com/dbnschools/moodle-theme_fordson theme/fordson
git submodule add https://github.com/willianmano/moodle-theme_moove theme/moove
git submodule add https://github.com/gavgeris/moodle-theme_etwinning theme/etwinning

#locals
git submodule add https://github.com/moodle-an-hochschulen/moodle-local_boostnavigation local/boostnavigation
git submodule add https://gitlab.com/adapta/moodle-local_modcustomfields local/modcustomfields
git submodule add https://github.com/moodle-an-hochschulen/moodle-local_staticpage local/staticpage

#enrolment
git submodule add https://github.com/bobopinna/moodle-enrol_autoenrol enrol/autoenrol

