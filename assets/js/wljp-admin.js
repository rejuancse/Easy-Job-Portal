jQuery(document).ready(function($) {
	try {
		$("#post").validate();
	} catch (e) {}

	var wljpJobSalary = $('.wljp_job_salary');
	var wljpJobSalaryValue = $('input[name="wljp_job_salary"]:checked').val();
	if(wljpJobSalaryValue == 'range') {
		wljpJobSalary.show();
	} else {
		wljpJobSalary.hide();
	}
	$('input[name="wljp_job_salary"]').on('change', function() {
		if(this.value == 'range') {
			wljpJobSalary.fadeIn();
		} else {
			wljpJobSalary.fadeOut();
		}
	});

	var wljpLastWorkingDay = $('.wljp_last_working_day');
	var wljpNoticePeriod = $('select[name="employee_work_experience_notice_period"]').val();
	if(wljpNoticePeriod == 'current') {
		wljpLastWorkingDay.show();
	} else {
		wljpLastWorkingDay.hide();
	}
	$('select[name="employee_work_experience_notice_period"]').on('change', function() {
		if(this.value == 'current') {
			wljpLastWorkingDay.fadeIn();
		} else {
			wljpLastWorkingDay.fadeOut();
		}
	});

	try {
		var wljpJobDepartments = $('#wljp_employee_desired_job_departments');
		var wljpJobDepartmentsPlaceholder = wljpJobDepartments.data('placeholder');
		wljpJobDepartments.fSelect({
			'placeholder': wljpJobDepartmentsPlaceholder
		});

		var wljpJobTypes = $('#wljp_employee_desired_job_types');
		var wljpJobTypesPlaceholder = wljpJobTypes.data('placeholder');
		wljpJobTypes.fSelect({
			'placeholder': wljpJobTypesPlaceholder
		});
	} catch (e) {}

	$(document).on('click', '#wljp_employee_education_row_add_more', function() {
		$('.wljp_employee_education_row').first().clone().find('input').attr({ value: '' }).end().appendTo('#wljp_employee_education_rows');
	});
	$(document).on('click', '.employee_education_remove_label', function() {
		if ( $('.wljp_employee_education_row').length > 1 ) {
			$(this).parent().parent().remove();
		}
	});

	$(document).on('click', '#wljp_employee_employment_row_add_more', function() {
		 $('.wljp_employee_employment_row').first().clone().find('input').attr({ value: '' }).end().appendTo('#wljp_employee_employment_rows');
	});
	$(document).on('click', '.employee_employment_remove_label', function() {
		if (  $('.wljp_employee_employment_row').length > 1 ) {
			$(this).parent().parent().remove();
		}
	});

	$(document).on('click', '#wljp_employee_certification_row_add_more', function() {
		$('.wljp_employee_certification_row').first().clone().find('input').attr({ value: '' }).end().appendTo('#wljp_employee_certification_rows');
	});
	$(document).on('click', '.employee_certification_remove_label', function() {
		if ( $('.wljp_employee_certification_row').length > 1 ) {
			$(this).parent().parent().remove();
		}
	});

	$(document).on('click', '#wljp_employee_skills_row_add_more', function() {
		$('.wljp_employee_skills_row').first().clone().find('input').attr({ value: '' }).end().appendTo('#wljp_employee_skills_rows');
	});
	$(document).on('click', '.employee_skills_remove_label', function() {
		if ( $('.wljp_employee_skills_row').length > 1 ) {
			$(this).parent().parent().remove();
		}
	});

	/* Copy target content to clipboard on click */
	function copyToClipboard(selector, target) {
		$(document).on('click', selector, function() {
			var value = $(target).text();
			var temp = $("<input>");
			$("body").append(temp);
			temp.val(value).select();
			document.execCommand("copy");
			temp.remove();
			toastr.success('Copied to clipboard.');
		});
	}

	copyToClipboard('#wljp_job_portal_shortcode_copy', '#wljp_job_portal_shortcode');
	copyToClipboard('#wljp_job_portal_account_shortcode_copy', '#wljp_job_portal_account_shortcode');
});