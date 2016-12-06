<?php

/**
 * Class LanguageSelectorPlugin
 */
class LanguageSelectorPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = [
        'initialize',
        'config_form',
        'config',
        'public_head',
	    'language_selector'
    ];

    protected $_filters = ['locale'];

    /**
     * Returns current locale and sets locale to current session
     *
     * @return bool|string
     */
    private function getCurrentLocale()
    {
        $session_locale = isset($_SESSION['current_locale']) ? $_SESSION['current_locale'] : false;
        $options_locale = get_option('current_locale');
        if (!$options_locale) {
            $options_locale = false;
        }

        $locale = $this->getDefaultLocale();
        if ($session_locale) {
            $locale = $session_locale;
        }
        elseif ($options_locale) {
            $locale = $options_locale;
        }

        $_SESSION['current_locale'] = $locale;
        return $locale;
    }

    /**
     * Loads language selector's assets
     */
    public function hookPublicHead()
    {
        queue_css_file('language_selector');
    }

    /**
     * Custom hook for displaying language selector widget
     */
    public function hookLanguageSelector()
    {
        $languages = $this->getLanguagesArray();

        $lang_vals = array_map( function ($item) { return $item[0]; }, $languages);
        $lang_labels = array_map( function ($item) { return $item[1]; }, $languages);

        $languages = array_combine($lang_vals, $lang_labels);
        $current_language = $this->getCurrentLocale();
        include('public_lang_selector.php');
    }

    /**
     * Omeka's filter "locale"
     *
     * @param $locale
     * @return bool|string
     */
    public function filterLocale($locale)
    {
        if (isset($_GET['language'])) {
            $locale = Zend_Locale::findLocale($_GET['language']);
            $_SESSION['current_locale'] = $locale;
            return $locale;
        }
        else {
            return $this->getCurrentLocale();
        }
    }

    /**
     * Plugin init
     */
    public function hookInitialize()
    {
        $this->getCurrentLocale();
    }

    public function hookConfig($args)
    {
        set_option('languages_list', $args['post']['languages_list_textarea']);
    }

    /**
     * Returns parsed languages list
     * @return array|string
     */
    public function getLanguagesArray()
    {
        $languages = get_option('languages_list');
        $languages = explode("\n", $languages);
        $languages = array_map(
            function ($item) {
                return explode(';', $item);
            },
            $languages
        );

        return $languages;
    }

    /**
     * Config form render
     */
    public function hookConfigForm()
    {
        $form = new Omeka_Form(
            [
                'type' => 'languages_list'
            ]
        );

        $languages = get_option('languages_list');
        if (!$languages) {
            $languages = "en_GB;English";
        }

        $form->addElement(
            'textarea',
            'languages_list_textarea',
            [
                'id' => 'simple-pages-title',
                'cols' => 50,
                'rows' => 25,
                'value' => $languages,
                'label' => __('Languages in select'),
                'description' => __(
                    'List of languages available from public in select. Each item in one line. Value and label separated by ";"'
                ),
                'required' => true
            ]
        );

        $form->removeDecorator('form');

        echo $form;

        echo "<h3>" . __('List of available locales:') . "</h3>";
        $locale_groups = [];
        $current_group = null;
        foreach (Zend_Locale::getLocaleList() as $locale => $v) {
            if (strpos($locale, '_') === false) {
                $current_group = $locale;
            } else {
                $locale_groups[$current_group][] = $locale;
            }
        }
        foreach ($locale_groups as $group => $locales) {
            echo "";
            foreach ($locales as $i => $l) {
                echo "<code>" . $l . "</code>";
                if ($i < count($locales) - 1) {
                    echo ', ';
                }
            }
            echo "<hr>";
        }
    }

    /**
     * Returns default locale (first element in the list)
     *
     * @return mixed
     */
    function getDefaultLocale()
    {
        return $this->getLanguagesArray()[0][0];
    }

}