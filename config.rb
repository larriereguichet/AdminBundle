# Require any additional compass plugins here.

# Set this to the root of your project when deployed:
http_path = "web"
css_dir = "Resources/public/css"
sass_dir = "Resources/public/scss"
images_dir = "Resources/public/img"
javascripts_dir = "Resources/public/js"

relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false
environment = :production

if environment == :production
  output_style = :compressed
else
  output_style = :expanded
  sass_options = { :debug_info => true }
end