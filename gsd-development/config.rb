# Require any additional compass plugins here.
#require "compass/utilities/sprites/_base"
#require "compass/css3/background-size"
require "sass-globbing"

# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "../gsd-resources/css"
sass_dir = "sass"
images_dir = "../gsd-resources/css/img"
generated_images_dir = "../gsd-resources/css/img"
images_dir_source = "../gsd-resources/css/img"
sprite_load_path = "../gsd-resources/css/img"
additional_import_paths  = ["../gsd-frontend/development/sass"]

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed

output_style = :compressed

#sourcemap = true

# To enable relative paths to assets via compass helper functions. Uncomment:
# relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
# line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass sass scss && rm -rf sass && mv scss sass


# Renames generated icon sprites - see http://stackoverflow.com/a/16478392/486434
#
#module Compass::SassExtensions::Functions::Sprites
#  def sprite_url(map)
#    #verify_map(map, "url")
#    verify_map(map, "sprite-url")
#    map.generate
#    generated_image_url(Sass::Script::String.new(map.name_and_hash))
#  end
#end
#
#module Compass::SassExtensions::Sprites::SpriteMethods
#  def name_and_hash
#    #"#{path}.png"
#    "sprite-#{path}.png"
#  end
#
#  def cleanup_old_sprites
#    #Dir[File.join(::Compass.configuration.generated_images_path, "#{path}.png")].each do |file|
#    Dir[File.join(::Compass.configuration.generated_images_path, "sprite-#{path}.png")].each do |file|
#      log :remove, file
#      FileUtils.rm file
#      ::Compass.configuration.run_sprite_removed(file)
#    end
#  end
#end
#
#module Compass
#  class << SpriteImporter
#    def find_all_sprite_map_files(path)
#      #glob = "*{#{self::VALID_EXTENSIONS.join(",")}}"
#      glob = "sprite-*{#{self::VALID_EXTENSIONS.join(",")}}"
#      Dir.glob(File.join(path, "**", glob))
#    end
#  end
#end
