module SelectHelper
  def fill_in_autocomplete(field, with:)
    input_field = find(:fillable_field, field)
    input_field.send_keys(with)
    input_field.native.send_keys(:return)
    sleep(1)
  end

  def select_from_autocomplete(field, options = {})
    fill_in(field, with: options[:with])
    page.execute_script %Q{ jQuery('##{field}').trigger('focus') }
    page.execute_script %Q{ jQuery('##{field}').trigger('keydown') }
    sleep(1)

    expect(page).to have_selector('ul.ui-autocomplete li.ui-menu-item')

    selector = 'ul.ui-autocomplete li.ui-menu-item:first-of-type'
    page.execute_script %Q{ jQuery('#{selector}').trigger('mouseenter').click() }
  end
end

World(SelectHelper)
