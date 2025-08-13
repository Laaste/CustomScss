<?php
function asset($path, $url = true)
{
	$basePath = "/{$path}";

	if($url)
	{
		return get_template_directory_uri() . $basePath;
	}
	else
	{ // abs path
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);

		return get_template_directory() . $path;
	}
}

/**
 * @param bool $url on true abs path on false
 * @param bool $min .min.css on true .css on false
 * 
 * @return string
 */
function getTemplateCssPath($template, $url = true, $min = true)
{
	$path = "css/theme/{$template}";

	if($min)
	{
		$path .= '.min.css';
	}
	else
	{
		$path .= '.css';
	}

	return asset($path, $url);
}

function getCssPathForTemplate()
{
	// Get the current template
	$currentTemplate = get_page_template_slug();

	if(! $currentTemplate)
	{
		// Default template
		if(is_home())
		{
			$currentTemplate = 'home';
		}
		elseif(is_front_page())
		{
			$currentTemplate = 'front-page';
		}
		elseif(is_single())
		{
			$currentTemplate = basename(get_single_template());
		}
		elseif(is_page())
		{
			$currentTemplate = 'page';
		}
		elseif(is_category())
		{
			$currentTemplate = 'category';
		}
		elseif(is_tag())
		{
			$currentTemplate = 'tag';
		}
		elseif(is_author())
		{
			$currentTemplate = 'author';
		}
		elseif(is_search())
		{
			$currentTemplate = 'search';
		}
		elseif(is_404())
		{
			$currentTemplate = '404';
		}
		elseif(is_tax())
		{
			$currentTemplate = basename(get_taxonomy_template());
		}
		else
		{
			$currentTemplate = 'index';
		}
	}

	if(substr($currentTemplate, -4) === '.php')
	{
		$currentTemplate = substr($currentTemplate, 0, -4);
	}

	// Create the corresponding CSS file path
	$cssFileUrl = getTemplateCssPath($currentTemplate, true, true);
	$cssFilePath = getTemplateCssPath($currentTemplate, false, true);

	// Check if the .min.css file exists
	if(file_exists($cssFilePath))
	{
		return $cssFileUrl;
	}
	else //Try without .min
	{
		$cssFileUrl = getTemplateCssPath($currentTemplate, true, false);
		$cssFilePath = getTemplateCssPath($currentTemplate, false, false);

		// Check if the CSS file exists
		if(file_exists($cssFilePath))
		{
			return $cssFileUrl;
		}
		else
		{
			// Fallback to a default CSS file ifthe specific template CSS does not exist
			$cssForTemplatePath = get_stylesheet_directory_uri() . '/css/theme/default.css';
			$cssDefaultForTemplate = get_stylesheet_directory() . '/css/theme/default.css';

			if(file_exists($cssDefaultForTemplate))
			{
				return $cssForTemplatePath;
			}
			else
			{
				return '';
			}
		}
	}
}

// Automatic handle before custom.scss
function theme_enqueue_template_styles()
{
	$cssPathForTemplate = getCssPathForTemplate();
	$currentTemplate = get_page_template_slug();

	if(! empty($getCssPathForTemplate))
	{
		wp_enqueue_style($currentTemplate . '-style', $cssPathForTemplate);
	}
}
add_action('wp_enqueue_scripts', 'theme_enqueue_template_styles');