<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

use setasign\Fpdi\Fpdi;

class UFPDI extends Fpdi {
	protected $wpenon_colors = array();
	protected $wpenon_fonts = array();
	protected $wpenon_lineheight = 0;

	protected $wpenon_current_font = '';

	protected $angle = 0;

	public function SetPageDrawColor( $name ) {
		if ( isset( $this->wpenon_colors[ $name ] ) ) {
			$this->SetDrawColor( $this->wpenon_colors[ $name ][0], $this->wpenon_colors[ $name ][1], $this->wpenon_colors[ $name ][2] );
		}
	}

	public function SetPageTextColor( $name ) {
		if ( isset( $this->wpenon_colors[ $name ] ) ) {
			$this->SetTextColor( $this->wpenon_colors[ $name ][0], $this->wpenon_colors[ $name ][1], $this->wpenon_colors[ $name ][2] );
		}
	}

	public function SetPageFillColor( $name ) {
		if ( isset( $this->wpenon_colors[ $name ] ) ) {
			$this->SetFillColor( $this->wpenon_colors[ $name ][0], $this->wpenon_colors[ $name ][1], $this->wpenon_colors[ $name ][2] );
		}
	}

	public function SetPageFont( $name ) {
		if ( isset( $this->wpenon_fonts[ $name ] ) ) {
			$this->SetFont( $this->wpenon_fonts[ $name ][0], $this->wpenon_fonts[ $name ][1], $this->wpenon_fonts[ $name ][2] );
			$this->wpenon_lineheight   = $this->wpenon_fonts[ $name ][3];
			$this->wpenon_current_font = $name;
		}
	}

	public function WriteCell( $text, $align, $ln, $width, $height = null, $fill = false, $border = false ) {
		if ( $height === null ) {
			$height = $this->wpenon_lineheight;
		}
		$changed        = false;
		$orig_font_size = $font_size = $this->FontSizePt;
		if ( $width > 0 ) {
			while ( $this->GetStringWidth( $text ) > $width ) {
				$this->SetFontSize( $font_size -= 2 );
				$changed = true;
			}
		}
		if ( $border === false ) {
			$border = 0;
		}
		$this->Cell( $width, $height, $text, $border, $ln, $align, $fill );
		if ( $changed ) {
			$this->SetFontSize( $orig_font_size );
		}
	}

	public function WriteMultiCell( $text, $align, $ln, $width, $height = null, $fill = false, $line_count = 0, $border = false ) {
		if ( $height === null ) {
			$height = $this->wpenon_lineheight;
		}
		$changed        = false;
		$orig_font_size = $font_size = $this->FontSizePt;
		if ( $width > 0 && $line_count > 0 ) {
			while ( $this->GetStringWidth( $text ) > $width * $line_count ) {
				$this->SetFontSize( $font_size -= 0.2 );
				$changed = true;
			}
		}
		if ( $border === false ) {
			$border = 0;
		}
		$this->MultiCell( $width, $height, $text, $border, $align, $fill );
		if ( $changed ) {
			$this->SetFontSize( $orig_font_size );
		}
		if ( $ln > 0 ) {
			$this->Ln( $height );
		}
	}

	public function WriteBoundedImage( $filepath, $xpos, $ypos, $max_width, $max_height ) {
		if ( ! empty( $filepath ) && file_exists( $filepath ) ) {
			$xpos       += 0.5;
			$ypos       += 0.5;
			$max_width  -= 1;
			$max_height -= 1;
			$size       = getImageSize( $filepath );
			if ( $size[0] > $size[1] || ( $size[0] < $size[1] && ( $max_height * ( $size[0] / $size[1] ) ) > $max_width ) ) {
				$ypos   = floor( $ypos + ( $max_height - ( $max_width * ( $size[1] / $size[0] ) ) ) / 2 );
				$xwidth = $max_width;
				$ywidth = 0;
			} else {
				$xpos   = floor( $xpos + ( $max_width - ( $max_height * ( $size[0] / $size[1] ) ) ) / 2 );
				$xwidth = 0;
				$ywidth = $max_height;
			}
			$this->Image( $filepath, $xpos, $ypos, $xwidth, $ywidth );
		}
	}

	public function Rotate( $angle, $x = - 1, $y = - 1 ) {
		if ( $x == - 1 ) {
			$x = $this->x;
		}
		if ( $y == - 1 ) {
			$y = $this->y;
		}
		if ( $this->angle != 0 ) {
			$this->_out( 'Q' );
		}
		$this->angle = $angle;
		if ( $angle != 0 ) {
			$angle *= M_PI / 180;
			$c     = cos( $angle );
			$s     = sin( $angle );
			$cx    = $x * $this->k;
			$cy    = ( $this->h - $y ) * $this->k;
			$this->_out( sprintf( 'q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, - $s, $c, $cx, $cy, - $cx, - $cy ) );
		}
	}

	public function SetStyle( $tag, $enable ) {
		$style = '';
		foreach ( array( 'B', 'I', 'U' ) as $s ) {
			if ( $s == $tag ) {
				if ( $enable ) {
					$style .= $s;
				}
			} elseif ( strpos( $this->FontStyle, $s ) !== false ) {
				$style .= $s;
			}
		}
		$this->SetFont( '', $style );
	}

	public function _endpage() {
		if ( $this->angle != 0 ) {
			$this->angle = 0;
			$this->_out( 'Q' );
		}
		parent::_endpage();
	}
}
