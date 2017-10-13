Before do
  unless ($seed_done ||= false)
    puts '---> Seeding test users <---'

    add_user!(:agency_editor)
    add_user!(:regional)

    $seed_done = true
  end
end
