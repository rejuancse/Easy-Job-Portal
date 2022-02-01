jQuery(document).ready(function($) {
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

	try {
		var wljpJobTypes = $('#wljp_job_types');
		var wljpJobTypesPlaceholder = wljpJobTypes.data('placeholder');
		wljpJobTypes.fSelect({
			'placeholder': wljpJobTypesPlaceholder
		});

		var wljpJobIndustries = $('#wljp_job_industries');
		var wljpJobIndustriesPlaceholder = wljpJobIndustries.data('placeholder');
		wljpJobIndustries.fSelect({
			'placeholder': wljpJobIndustriesPlaceholder
		});

		var wljpJobDepartments = $('#wljp_job_departments');
		var wljpJobDepartmentsPlaceholder = wljpJobDepartments.data('placeholder');
		wljpJobDepartments.fSelect({
			'placeholder': wljpJobDepartmentsPlaceholder
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

	var wljpCVDelete = $('.wljp-cv-delete');
	wljpCVDelete.hide();
	$(document).on('click', '.wljp-cv-more-options', function() {
		wljpCVDelete.fadeToggle();
	});

	var wljpCompanyDelete = $('.wljp-company-delete');
	wljpCompanyDelete.hide();
	$(document).on('click', '.wljp-company-more-options', function() {
		wljpCompanyDelete.fadeToggle();
	});

	var wljpSignupHeading = $('.wljp-signup-heading span');
	var wljpSignupForm = $('#wljp-signup-form');

	$(document).on('click', '.wljp-signup-as-list a', function() {
		$(this).parent().parent().find('a').removeClass('active');
		$(this).addClass('active');
	});

	$(document).on('click', '#wljp-signup-as-recruiter', function() {
		var heading = $(this).data('heading');
		wljpSignupHeading.html(heading);
		wljpSignupForm.find('input[name="signup_as"]').val('recruiter');
	});

	$(document).on('click', '#wljp-signup-as-employee', function() {
		var heading = $(this).data('heading');
		wljpSignupHeading.html(heading);
		wljpSignupForm.find('input[name="signup_as"]').val('employee');
	});

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

	$(document).on('click', '#wljp-change-password-email-button', function() {
		$('.wljp-change-password-email').fadeToggle();
	});
});