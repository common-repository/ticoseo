/**
 * SASS
 */
import '../sass/backend.scss';

/**
 * JavaScript
 */

jQuery(document).ready(function ($) {
	const minTimeoutBetweenCallsInMilliSeconds = 700;
	const ideaTextarea = $('#oaipost_mass_prompt_idea');
	const ideaModel = $('#model_idea');
	const ideaTemp = $('#temperature_idea');
	const ideaFreq = $('#fpenalty_idea');
	const ideaMaxTokens = $('#maxtokens_idea');

	const outlineTextarea = $('#oaipost_mass_prompt_outline');
	const outlineModel = $('#model_outline');
	const outlineTemp = $('#temperature_outline');
	const outlineFreq = $('#fpenalty_outline');
	const outlineMaxTokens = $('#maxtokens_outline');

	const contentTextarea = $('#oaipost_mass_prompt_content');
	const contentModel = $('#model_content');
	const contentTemp = $('#temperature_content');
	const contentFreq = $('#fpenalty_content');
	const contentMaxTokens = $('#maxtokens_content');

	$('#generate-lead-btn').click(function (e) {
		e.preventDefault();

		$('#generate-lead-btn').addClass('button--loading');

		const prompt = $('textarea#oaipost_mass_prompt_idea').val();

		const data = {
			action: 'generate_idea',
			prompt,
		};

		$.post(ajaxurl, data, function (response) {
			if (response) {
				$(this).removeClass('button--loading');
				window.location.reload();
			}
		});
	});

	$('#doaction').click(function () {
		const top_action = $('#bulk-action-selector-top').val();
		const bottom_action = $('#bulk-action-selector-bottom').val();

		if (
			top_action === 'create_article_outlines' ||
			bottom_action === 'create_article_outlines'
		) {
			$('i.c-inline-spinner.outline').css('display', 'inline-block');
		}

		if (
			top_action === 'generate_article' ||
			bottom_action === 'generate_article'
		) {
			$('i.c-inline-spinner.generate').css('display', 'inline-block');
		}
	});

	const saveByAjax = function () {
		console.log('saving ajax');

		const data = {
			action: this.action,
			value: this.value,
		};

		$.post(ajaxurl, data, function (response) {
			if (response) {
				console.log('saved');
			}
		});
	};

	const oaiHeader = {
		action: 'undefined',
		value: null,
		save: debounce(saveByAjax, minTimeoutBetweenCallsInMilliSeconds),
	};

	ideaTextarea.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_idea_prompt';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	ideaModel.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_idea_model';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	ideaTemp.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_idea_temp';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	ideaFreq.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_idea_freq';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	ideaMaxTokens.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_idea_maxtokens';
		oaiHeader.value = value;
		oaiHeader.save();
	});

	outlineTextarea.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_outline_prompt';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	outlineModel.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_outline_model';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	outlineTemp.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_outline_temp';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	outlineFreq.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_outline_freq';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	outlineMaxTokens.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_outline_maxtokens';
		oaiHeader.value = value;
		oaiHeader.save();
	});

	contentTextarea.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_content_prompt';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	contentModel.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_content_model';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	contentTemp.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_content_temp';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	contentFreq.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_content_freq';
		oaiHeader.value = value;
		oaiHeader.save();
	});
	contentMaxTokens.on('change', function () {
		const value = $(this).val();
		oaiHeader.action = 'save_oai_content_maxtokens';
		oaiHeader.value = value;
		oaiHeader.save();
	});

	function debounce(func, wait, immediate) {
		let timeout;
		return function () {
			const context = this,
				args = arguments;
			const later = function () {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			const callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	}
});
