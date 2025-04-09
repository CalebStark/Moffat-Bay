package com.example.moffatbay.servlets;

import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import com.google.gson.JsonObject;

public class SummaryServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        response.setContentType("application/json");
        PrintWriter out = response.getWriter();
        HttpSession session = request.getSession();

        try {
            // 1. Retrieve data from session
            String boatLength = (String) session.getAttribute("boatLength");
            String boatName = (String) session.getAttribute("boatName");
            String checkInDate = (String) session.getAttribute("checkInDate");

            // 2. Create JSON response
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("boatLength", boatLength);
            jsonOutput.addProperty("boatName", boatName);
            jsonOutput.addProperty("checkInDate", checkInDate);

            // 3. Send JSON response
            out.print(jsonOutput.toString());
            out.flush();

        } catch (Exception e) {
            response.setStatus(500); // Internal Server Error
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Error retrieving session data.");
            out.print(jsonOutput.toString());
            out.flush();
        }
    }
}
