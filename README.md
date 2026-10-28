# Kkkonrad_Brands

Manufacturers: a logo row on the home page, `/producenci` (all brands A-Z) and
`/producenci/<slug>` (that brand's products), Hyvä and Luma.

- **Data:** a brand is an option of core's `manufacturer` attribute. The data patch makes
  it a **visual swatch** and adds it to every attribute set - the swatch image is the logo
  (original upload, not the 30x20 thumbnail). No tables of its own.
- **Admin:** Stores > Attributes > Product > `manufacturer` > add an option, upload the
  logo as its swatch. Set the brand on products (Product Details) or via mass
  "Update attributes". Saving the attribute purges pages that list brands (cache tag).
- **Brand page:** the theme's own product list, toolbar and cards (block tree copied from
  core's advanced search result) over a collection filtered by `manufacturer`;
  visible-in-catalog, in-stock only. No layered navigation.
- **URLs:** slug = transliterated label (`Tundra Co.` -> `tundra-co`), resolved by
  `Controller\Router` before the standard router. Unknown slug -> 404.
