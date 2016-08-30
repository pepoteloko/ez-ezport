<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no" />
	<xsl:strip-space elements="*" />

	<xsl:template match="section">
		<html>
			<body>
				<xsl:apply-templates select="node()|@*"/>
			</body>
		</html>
	</xsl:template>

	<xsl:template match="node()|@*">
		<xsl:copy>
			<xsl:apply-templates select="node()|@*"/>
		</xsl:copy>
	</xsl:template>

	<!-- Arreglamos los tags del EZ -->
	<xsl:template match="paragraph">
		<p>
			<xsl:apply-templates />
		</p>
	</xsl:template>
	<xsl:template match="emphasize">
		<em>
		<xsl:apply-templates />
		</em>
	</xsl:template>
	<xsl:template match="header">
		<h2>
		<xsl:apply-templates />
		</h2>
	</xsl:template>
	<xsl:template match="link">
		<xsl:element name="a">
			<xsl:attribute name="href">
				<xsl:value-of select="@url_id"/>
			</xsl:attribute>
			<xsl:attribute name="target">
				<xsl:value-of select="@target"/>
			</xsl:attribute>
			<xsl:apply-templates />
		</xsl:element>
	</xsl:template>
	<!--
		Estos en teoria como son igual en HTML podemos omitirlos pues deberian copiarse igual que estan en el origen
	<xsl:template match="ul">
		<ul>
		<xsl:apply-templates />
		</ul>
	</xsl:template>
	<xsl:template match="li">
		<li>
		<xsl:apply-templates />
		</li>
	</xsl:template>
	<xsl:template match="ol">
		<ol>
		<xsl:apply-templates />
		</ol>
	</xsl:template>
	<xsl:template match="table">
		<table>
		<xsl:apply-templates />
		</table>
	</xsl:template>
	<xsl:template match="td">
		<td>
		<xsl:apply-templates />
		</td>
	</xsl:template>
	<xsl:template match="tr">
		<tr>
		<xsl:apply-templates />
		</tr>
	</xsl:template>
	-->
</xsl:stylesheet>